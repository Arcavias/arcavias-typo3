<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias scheduler for periodic tasks of the shop.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_scheduler_maintenance
	extends tx_arcavias_scheduler_abstract
	implements tx_scheduler_AdditionalFieldProvider
{
	private $_fieldEmail = 'arcavias_maintenance_email';
	private $_fieldName = 'arcavias_maintenance_name';


	/**
	 * Function executed by the scheduler.
	 *
	 * @return boolean True if success, false if not
	 */
	public function execute()
	{
		try
		{
			$context = $this->_getContext();
		}
		catch( Exception $e )
		{
			error_log( 'Unable to create context: ' . $e->getMessage() );
			error_log( $e->getTraceAsString() );

			return false;
		}


		try
		{
			$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
			$siteManager = $localeManager->getSubManager( 'site' );

			$siteSearch = $siteManager->createSearch( true );
			$expr = array(
				$siteSearch->getConditions(),
				$siteSearch->compare( '==', 'locale.site.level', 0 ),
			);
			$siteSearch->setConditions( $siteSearch->combine( '&&', $expr ) );

			foreach( $siteManager->searchItems( $siteSearch ) as $siteItem )
			{
				$level = MShop_Locale_Manager_Abstract::SITE_SUBTREE;
				$locale = $localeManager->bootstrap( $siteItem->getCode(), '', '', false, $level );

				$locale->setLanguageId( null );
				$locale->setCurrencyId( null );
				$context->setLocale( $locale );

				$this->_updateStock( $context );
				$this->_sendEmails( $context );
				$this->_processDelivery( $context );
				$this->_executeJobs( $context );
			}
		}
		catch( Exception $e )
		{
			$context->getLogger()->log( 'Error executing maintenance scheduler: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() );
			return false;
		}

		return true;
	}


	/**
	 * Fields generation.
	 * This method is used to define new fields for adding or editing a task
	 * In this case, it adds a page ID field
	 *
	 * @param array $taskInfo Reference to the array containing the info used in the add/edit form
	 * @param object $task When editing, reference to the current task object. Null when adding.
	 * @param tx_scheduler_Module $parentObject Reference to the calling object (Scheduler's BE module)
	 * @return array Array containg all the information pertaining to the additional fields
	 *		The array is multidimensional, keyed to the task class name and each field's id
	 *		For each field it provides an associative sub-array with the following:
	 *			['code']		=> The HTML code for the field
	 *			['label']		=> The label of the field (possibly localized)
	 *			['cshKey']		=> The CSH key for the field
	 *			['cshLabel']	=> The code of the CSH label
	 */
	public function getAdditionalFields( array &$taskInfo, $task, tx_scheduler_Module $parentObject )
	{
		$additionalFields = array();

		// In case of editing a task, set to the internal value if data wasn't already submitted
		if( empty( $taskInfo[$this->_fieldEmail] ) && $parentObject->CMD === 'edit' ) {
			$taskInfo[$this->_fieldEmail] = $task->{$this->_fieldEmail};
		}

		$fieldCode = sprintf( '<input name="tx_scheduler[%1$s]" id="%1$s" value="%2$s" />', $this->_fieldEmail, $taskInfo[$this->_fieldEmail] );

		$additionalFields[$this->_fieldEmail] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:maintenance.label.email',
			'cshKey'   => 'xMOD_tx_arcavias',
			'cshLabel' => $this->_fieldEmail
		);


		// In case of editing a task, set to the internal value if data wasn't already submitted
		if( empty( $taskInfo[$this->_fieldName] ) && $parentObject->CMD === 'edit' ) {
			$taskInfo[$this->_fieldName] = $task->{$this->_fieldName};
		}

		$fieldCode = sprintf( '<input name="tx_scheduler[%1$s]" id="%1$s" value="%2$s" />', $this->_fieldName, $taskInfo[$this->_fieldName] );

		$additionalFields[$this->_fieldName] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:maintenance.label.name',
			'cshKey'   => 'xMOD_tx_arcavias',
			'cshLabel' => $this->_fieldName
		);

		return $additionalFields;
	}


	/**
	 * Store fields.
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param array $submittedData Array containing the data submitted by the user
	 * @param tx_scheduler_Task	$task Reference to the current task object
	 */
	public function saveAdditionalFields( array $submittedData, tx_scheduler_Task $task )
	{
		$task->{$this->_fieldEmail} = $submittedData[$this->_fieldEmail];
		$task->{$this->_fieldName} = $submittedData[$this->_fieldName];
	}


	/**
	 * Fields validation.
	 * This method checks if page id given in the 'Hide content' specific task is int+
	 * If the task class is not relevant, the method is expected to return true
	 *
	 * @param array $submittedData Reference to the array containing the data submitted by the user
	 * @param tx_scheduler_Module $parentObject Reference to the calling object (Scheduler's BE module)
	 * @return boolean True if validation was ok (or selected class is not relevant), false otherwise
	 */
	public function validateAdditionalFields( array &$submittedData, tx_scheduler_Module $parentObject )
	{
		if( strpos( $submittedData[$this->_fieldEmail], '@' ) > 0 ) {
			return true;
		}

		$message = $GLOBALS['LANG']->sL('LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:maintenance.error.email');
		$parentObject->addMessage( $message, t3lib_FlashMessage::ERROR );

		return false;
	}


	/**
	 * Executes all jobs of a given site.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with locale item set
	 * @throws Exception If there are problems getting the jobs
	 */
	protected function _executeJobs( MShop_Context_Item_Interface $context )
	{
		$jobManager = MAdmin_Job_Manager_Factory::createManager( $context );
		$criteria = $jobManager->createSearch( true );

		$start = 0;

		do
		{
			$items = $jobManager->searchItems( $criteria );

			foreach( $items as $item )
			{
				try
				{
					$job = $item->getMethod();

					if( preg_match( '/^[a-zA-Z0-9\_]+\.[a-zA-Z0-9\_]+$/', $job ) !== 1 ) {
						throw new Exception( sprintf( 'Invalid job name "%1$s"', $job ) );
					}

					$parts = explode( '.', $job );

					if( count( $parts ) !== 2 ) {
						throw new Exception( sprintf( 'Invalid job method "%1$s"', $job ) );
					}

					$name = "Controller_ExtJS_{$parts[0]}_Factory";
					$method = $parts[1];

					if( class_exists( $name ) === false ) {
						throw new Exception( sprintf( 'Class "%1$s" not found', $name ) );
					}

					$name .= '::createController';

					if( ( $controller = call_user_func_array( $name, array( $context ) ) ) === false ) {
						throw new Exception( sprintf( 'Unable to call factory method "%1$s"', $name ) );
					}

					if( method_exists( $controller, $method ) === false ) {
						throw new Exception( sprintf( 'Method "%1$s" not found', $method ) );
					}

					$result = $controller->$method( (object) $item->getParameter() );

					$item->setResult( $result );
					$item->setStatus( 0 );
				}
				catch( Exception $e )
				{
					$str = 'Error while processing job "%1$s": %2$s';
					$context->getLogger()->log( sprintf( $str, $item->getMethod(), $e->getMessage() ) );
					$item->setStatus( -1 );
				}

				$jobManager->saveItem( $item );
			}

			$count = count( $items );
			$start += $count;
			$criteria->setSlice( $start );
		}
		while( $count > 0 );
	}


	/**
	 * Calls the delivery service providers to process the new orders.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with locale item set
	 * @throws Exception If there are problems getting the orders
	 */
	protected function _processDelivery( MShop_Context_Item_Interface $context )
	{
		$serviceManager = MShop_Service_Manager_Factory::createManager( $context );
		$serviceSearch = $serviceManager->createSearch();

		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderSearch = $orderManager->createSearch();

		$start = 0;

		do
		{
			$serviceItems = $serviceManager->searchItems( $serviceSearch );

			foreach( $serviceItems as $serviceItem )
			{
				try
				{
					$serviceProvider = $serviceManager->getProvider( $serviceItem );

					$expr = array(
						$orderSearch->compare( '==', 'order.siteid', $serviceItem->getSiteId() ),
						// @todo: make this value configurable
						$orderSearch->compare( '>', 'order.datepayment', date( 'Y-m-d 00:00:00', time() - 86400 * 90 ) ),
						$orderSearch->compare( '>', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_PENDING ),
						$orderSearch->compare( '==', 'order.statusdelivery', MShop_Order_Item_Abstract::STAT_UNFINISHED ),
						$orderSearch->compare( '==', 'order.base.service.code', $serviceItem->getCode() ),
						$orderSearch->compare( '==', 'order.base.service.type', 'delivery' ),
					);
					$orderSearch->setConditions( $orderSearch->combine( '&&', $expr ) );

					$orderStart = 0;

					do
					{
						$orderItems = $orderManager->searchItems( $orderSearch );

						foreach( $orderItems as $orderItem )
						{
							try
							{
								$serviceProvider->process( $orderItem );
								$orderManager->saveItem( $orderItem );
							}
							catch( Exception $e )
							{
								$str = 'Error while processing order with ID "%1$s": %2$s';
								$context->getLogger()->log( sprintf( $str, $orderItem->getId(), $e->getMessage() ) );
							}
						}

						$orderCount = count( $orderItems );
						$orderStart += $orderCount;
						$orderSearch->setSlice( $orderStart );
					}
					while( $orderCount > 0 );
				}
				catch( Exception $e )
				{
					$str = 'Error while processing service with ID "%1$s": %2$s';
					$context->getLogger()->log( sprintf( $str, $serviceItem->getId(), $e->getMessage() ) );
				}
			}

			$count = count( $serviceItems );
			$start += $count;
			$serviceSearch->setSlice( $start );
		}
		while( $count > 0 );
	}


	/**
	 * Sends the confirmation e-mail for all new orders.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with locale item set
	 * @throws Exception If there are problems getting the orders
	 */
	protected function _sendEmails( MShop_Context_Item_Interface $context )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseManager = $orderManager->getSubManager( 'base' );

		$orderSearch = $orderManager->createSearch();
		$expr = array(
			$orderSearch->compare( '>', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_PENDING ),
			$orderSearch->combine( '!', array( $orderSearch->compare( '&', 'order.emailflag', MShop_Order_Item_Abstract::EMAIL_ACCEPTED ) ) ),
		);
		$orderSearch->setConditions( $orderSearch->combine( '&&', $expr ) );

		$i18nPaths = $this->_getArcavias()->getI18nPaths();
		$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
		$client = Client_Html_Email_Confirm_Factory::createClient( $context, $templatePaths );
		$mainClient = $client->getSubClient( 'main' );
		$htmlClient = $mainClient->getSubClient( 'html' );
		$textClient = $mainClient->getSubClient( 'text' );
		$view = $this->_createView();

		$start = 0;
		$mail = t3lib_div::makeInstance( 't3lib_mail_Message' );

		do
		{
			$items = $orderManager->searchItems( $orderSearch );

			foreach( $items as $item )
			{
				try
				{
					$orderBaseItem = $orderBaseManager->load( $item->getBaseId() );
					$view->confirmOrderBaseItem = $orderBaseItem;
					$view->confirmOrderItem = $item;

					$addr = $orderBaseItem->getAddress( MShop_Order_Item_Base_Address_Abstract::TYPE_BILLING );

					$trans = new MW_Translation_Zend( $i18nPaths, 'gettext', $addr->getLanguageId(), array( 'disableNotices' => true ) );
					$helper = new MW_View_Helper_Translate_Default( $view, $trans );
					$view->addHelper( 'translate', $helper );

					$htmlClient->setView( $view );
					$textClient->setView( $view );

					$name = sprintf( $view->translate( 'client/html', '%1$s %2$s' ), $addr->getFirstname(), $addr->getLastname() );

					$mail->setFrom( array( $this->{$this->_fieldEmail} => $this->{$this->_fieldName} ) );
					$mail->setTo( array( $addr->getEmail() => $name ) );
					$mail->setSubject( sprintf( $view->translate( 'client/html', 'Confirmation for order %1$s' ), $item->getId() ) );
					$mail->setBody( $textClient->getBody() );
					$mail->addPart( $htmlClient->getBody(), 'text/html' );
					$mail->send();

					$item->setEMailFlag( $item->getEMailFlag() | MShop_Order_Item_Abstract::EMAIL_ACCEPTED );
					$orderManager->saveItem( $item );
				}
				catch( Exception $e )
				{
					$str = 'Error while trying to send confirmation e-mail for order ID "%1$s": %2$s';
					$context->getLogger()->log( sprintf( $str, $item->getId(), $e->getMessage() ) );
				}
			}

			$count = count( $items );
			$start += $count;
			$orderSearch->setSlice( $start );
		}
		while( $count > 0 );
	}


	/**
	 * Updates the stock level for all ordered products.
	 *
	 * @param MShop_Context_Item_Interface $context Context object with locale item set
	 * @throws Exception If there are problems getting the orders
	 */
	protected function _updateStock( MShop_Context_Item_Interface $context )
	{
		$orderManager = MShop_Order_Manager_Factory::createManager( $context );
		$orderBaseProductManager = $orderManager->getSubManager( 'base' )->getSubManager( 'product' );
		$stockManager = MShop_Product_Manager_Factory::createManager( $context )->getSubManager( 'stock' );

		$orderProductSearch = $orderBaseProductManager->createSearch();
		$orderProductSearch->setSlice( 0, 0x7fffffff );

		$orderSearch = $orderManager->createSearch();
		$expr = array(
			$orderSearch->compare( '>=', 'order.statuspayment', MShop_Order_Item_Abstract::PAY_AUTHORIZED ),
			$orderSearch->combine( '!', array( $orderSearch->compare( '&', 'order.flag', MShop_Order_Item_Abstract::FLAG_STOCK ) ) ),
		);
		$orderSearch->setConditions( $orderSearch->combine( '&&', $expr ) );

		$start = 0;
		$siteConfig = $context->getLocale()->getSite()->getConfig();
		/** @todo Repository configuration in sub-sites? */
		$repository = ( isset( $siteConfig['repository'] ) ? $siteConfig['repository'] : 'default' );

		do
		{
			$items = $orderManager->searchItems( $orderSearch );

			foreach( $items as $item )
			{
				$this->_beginTx();

				try
				{
					$orderProductSearch->setConditions( $orderProductSearch->compare( '==', 'order.base.product.baseid',  $item->getBaseId() ) );

					foreach( $orderBaseProductManager->searchItems( $orderProductSearch ) as $orderProductItem ) {
						$stockManager->decrease( $orderProductItem->getProductCode(), $repository, $orderProductItem->getQuantity() );
					}

					$item->setFlag( $item->getFlag() | MShop_Order_Item_Abstract::FLAG_STOCK );
					$orderManager->saveItem( $item );

					$this->_commitTx();
				}
				catch( Exception $e )
				{
					$this->_rollbackTx();
					$str = 'Error while updating stock for order ID "%1$s": %2$s';
					$context->getLogger()->log( sprintf( $str, $item->getId(), $e->getMessage() ) );
				}

			}

			$count = count( $items );
			$start += $count;
			$orderSearch->setSlice( $start );
		}
		while( $count > 0 );
	}
}
