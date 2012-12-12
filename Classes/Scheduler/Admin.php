<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias scheduler for jobs created by the admin interface.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_scheduler_admin
	extends tx_arcavias_scheduler_abstract
{
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

				$this->_executeJobs( $context );
			}
		}
		catch( Exception $e )
		{
			$context->getLogger()->log( 'Error executing admin scheduler: ' . $e->getMessage() );
			return false;
		}

		return true;
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
}
