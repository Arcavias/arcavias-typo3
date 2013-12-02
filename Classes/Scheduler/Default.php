<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias scheduler.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_scheduler_default
	extends tx_arcavias_scheduler_abstract
	implements tx_scheduler_AdditionalFieldProvider
{
	private $_fieldSite = 'arcavias_sitecode';
	private $_fieldController = 'arcavias_controller';
	private $_fieldTSconfig = 'arcavias_config';


	/**
	 * Function executed by the scheduler.
	 *
	 * @return boolean True if success, false if not
	 */
	public function execute()
	{
		try
		{
			$context = $this->_getContext( $this->_parseTS( $this->{$this->_fieldTSconfig} ) );
		}
		catch( Exception $e )
		{
			error_log( 'Unable to create context: ' . $e->getMessage() );
			error_log( $e->getTraceAsString() );

			return false;
		}


		try
		{
			$arcavias = $this->_getArcavias();
			$manager = MShop_Locale_Manager_Factory::createManager( $context );

			$langid = 'en';
			if( isset( $GLOBALS['BE_USER']->user['lang'] ) && $GLOBALS['BE_USER']->user['lang'] != '' ) {
				$langid = $GLOBALS['BE_USER']->user['lang'];
			}

			foreach( (array) $this->{$this->_fieldSite} as $sitecode )
			{
				$localeItem = $manager->bootstrap( $sitecode, $langid, '', false );
				$context->setLocale( $localeItem );

				foreach( (array) $this->{$this->_fieldController} as $name ) {
					Controller_Jobs_Factory::createController( $context, $arcavias, $name )->run();
				}
			}
		}
		catch( Exception $e )
		{
			$logger = $context->getLogger();
			$logger->log( 'Error executing Arcavias scheduler: ' . $e->getMessage() );
			$logger->log( $e->getTraceAsString() );

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

		try
		{
			// In case of editing a task, set to the internal value if data wasn't already submitted
			if( empty( $taskInfo[$this->_fieldController] ) && $parentObject->CMD === 'edit' ) {
				$taskInfo[$this->_fieldController] = $task->{$this->_fieldController};
			}

			$taskInfo[$this->_fieldController] = (array) $taskInfo[$this->_fieldController];

			$fieldCode = sprintf( '<select name="tx_scheduler[%1$s][]" id="%1$s" multiple="multiple" size="10" />', $this->_fieldController );
			$fieldCode .= $this->_getControllerOptions( $taskInfo[$this->_fieldController] );
			$fieldCode .= '</select>';

			$additionalFields[$this->_fieldController] = array(
				'code'     => $fieldCode,
				'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.label.controller',
				'cshKey'   => 'xMOD_tx_arcavias',
				'cshLabel' => $this->_fieldController
			);


			// In case of editing a task, set to the internal value if data wasn't already submitted
			if( empty( $taskInfo[$this->_fieldSite] ) && $parentObject->CMD === 'edit' ) {
				$taskInfo[$this->_fieldSite] = $task->{$this->_fieldSite};
			}

			$taskInfo[$this->_fieldSite] = (array) $taskInfo[$this->_fieldSite];

			$fieldCode = sprintf( '<select name="tx_scheduler[%1$s][]" id="%1$s" multiple="multiple" size="10" />', $this->_fieldSite );
			$fieldCode .= $this->_getSiteOptions( $this->_getAvailableSites(), $taskInfo[$this->_fieldSite], 0 );
			$fieldCode .= '</select>';

			$additionalFields[$this->_fieldSite] = array(
				'code'     => $fieldCode,
				'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.label.sitecode',
				'cshKey'   => 'xMOD_tx_arcavias',
				'cshLabel' => $this->_fieldSite
			);


			// In case of editing a task, set to the internal value if data wasn't already submitted
			if( empty( $taskInfo[$this->_fieldTSconfig] ) && $parentObject->CMD === 'edit' ) {
				$taskInfo[$this->_fieldTSconfig] = $task->{$this->_fieldTSconfig};
			}

			$taskInfo[$this->_fieldTSconfig] = (string) $taskInfo[$this->_fieldTSconfig];

			$fieldStr = '<textarea name="tx_scheduler[%1$s]" id="%1$s" rows="20" cols="80" >%2$s</textarea>';
			$fieldCode = sprintf( $fieldStr, $this->_fieldTSconfig, $taskInfo[$this->_fieldTSconfig] );

			$additionalFields[$this->_fieldTSconfig] = array(
				'code'     => $fieldCode,
				'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.label.tsconfig',
				'cshKey'   => 'xMOD_tx_arcavias',
				'cshLabel' => $this->_fieldTSconfig
			);
		}
		catch( Exception $e )
		{
			error_log( 'Error in Arcavias scheduler: ' . $e->getMessage() );
			error_log( $e->getTraceAsString() );
		}

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
		$task->{$this->_fieldSite} = $submittedData[$this->_fieldSite];
		$task->{$this->_fieldController} = $submittedData[$this->_fieldController];
		$task->{$this->_fieldTSconfig} = $submittedData[$this->_fieldTSconfig];
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
		try
		{
			if( count( (array) $submittedData[$this->_fieldController] ) < 1 ) {
				throw new Exception( $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.error.controller.missing' ) );
			}

			if( count( $submittedData[$this->_fieldSite] ) < 1 ) {
				throw new Exception( $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.error.sitecode.missing' ) );
			}

			$this->_parseTS( $submittedData[$this->_fieldTSconfig] );


			$context = $this->_getContext();


			$manager = MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'site' );

			$search = $manager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'locale.site.code', $submittedData[$this->_fieldSite] ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			if( count( $manager->searchItems( $search ) ) !== count( $submittedData[$this->_fieldSite] ) ) {
				throw new Exception( $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.error.sitecode' ) );
			}


			$arcavias = $this->_getArcavias();
			$cntlPaths = $arcavias->getCustomPaths( 'controller/jobs' );

			foreach( (array) $submittedData[$this->_fieldController] as $name ) {
				Controller_Jobs_Factory::createController( $context, $arcavias, $name );
			}


			return true;
		}
		catch( Exception $e )
		{
			$message = $e->getMessage();
		}

		$parentObject->addMessage( $message, t3lib_FlashMessage::ERROR );
		return false;
	}


	/**
	 * Returns the list of site trees.
	 *
	 * @return array Associative list of items and children implementing MShop_Locale_Item_Site_Interface
	 */
	protected function _getAvailableSites()
	{
		$context = $this->_getContext();
		$manager = MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'site' );

		$search = $manager->createSearch();
		$search->setConditions( $search->compare( '==', 'locale.site.level', 0 ) );
		$search->setSortations( array( $search->sort( '+', 'locale.site.label' ) ) );

		$sites = $manager->searchItems( $search );

		foreach( $sites as $id => $siteItem ) {
			$sites[$id] = $manager->getTree( $id, array(), MW_Tree_Manager_Abstract::LEVEL_TREE );
		}

		return $sites;
	}


	/**
	 * Returns the HTML code for the select control.
	 * The method adds every site and its children recursively.
	 *
	 * @param array $siteItems List of items implementing MShop_Locale_Item_Site_Interface
	 * @param array $selected List of site codes that were previously selected by the user
	 * @param integer $level Nesting level of the sites (should start with 0)
	 * @return string HTML code with <option> tags for the select box
	 */
	protected function _getSiteOptions( array $siteItems, array $selected, $level )
	{
		$html = '';
		$prefix = str_repeat( '-', $level ) . ' ';

		foreach( $siteItems as $item )
		{
			$active = ( in_array( $item->getCode(), $selected ) ? 'selected="selected"' : '' );
			$disabled = ( $item->getStatus() > 0 ? '' : 'disabled="disabled"' );
			$string = '<option value="%1$s" %2$s %3$s>%4$s</option>';
			$html .= sprintf( $string, $item->getCode(), $active, $disabled, $prefix . $item->getLabel() );

			$html .= $this->_getSiteOptions( $item->getChildren(), $selected, $level+1 );
		}

		return $html;
	}


	/**
	 * Returns the HTML code for the controller control.
	 *
	 * @param array $selected List of site codes that were previously selected by the user
	 * @return string HTML code with <option> tags for the select box
	 */
	protected function _getControllerOptions( array $selected )
	{
		$html = '';
		$arcavias = $this->_getArcavias();
		$cntlPaths = $arcavias->getCustomPaths( 'controller/jobs' );
		$controllers = Controller_Jobs_Factory::getControllers( $this->_getContext(), $arcavias, $cntlPaths );

		foreach( $controllers as $name => $controller )
		{
			$active = ( in_array( $name, $selected ) ? 'selected="selected"' : '' );
			$string = '<option value="%1$s" %2$s>%3$s</option>';
			$html .= sprintf( $string, $name, $active, $controller->getName() );
		}

		return $html;
	}
}
