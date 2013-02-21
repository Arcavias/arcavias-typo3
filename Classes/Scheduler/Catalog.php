<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias catalog index scheduler.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_scheduler_catalog
	extends tx_arcavias_scheduler_abstract
	implements tx_scheduler_AdditionalFieldProvider
{
	private $_fieldSite = 'arcavias_catalog_sitecode';


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
				$siteSearch->compare( '==', 'locale.site.code', $this->{$this->_fieldSite} ),
			);
			$siteSearch->setConditions( $siteSearch->combine( '&&', $expr ) );

			foreach( $siteManager->searchItems( $siteSearch ) as $siteItem )
			{
				try
				{
					$locale = $localeManager->bootstrap( $siteItem->getCode() );

					$locale->setLanguageId( null );
					$locale->setCurrencyId( null );
					$context->setLocale( $locale );

					$manager = MShop_Catalog_Manager_Factory::createManager( $context );
					$manager->getSubManager( 'index' )->rebuildIndex();
				}
				catch( Exception $e )
				{
					$str = 'Error processing site "%1$s" in catalog scheduler: %2$s';
					$context->getLogger()->log( sprintf( $str, $siteItem->getCode(), $e->getMessage() ) );
				}
			}
		}
		catch( Exception $e )
		{
			$context->getLogger()->log( 'Error executing catalog scheduler: ' . $e->getMessage() );
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
			if( empty( $taskInfo[$this->_fieldSite] ) && $parentObject->CMD === 'edit' ) {
				$taskInfo[$this->_fieldSite] = $task->{$this->_fieldSite};
			}

			$taskInfo[$this->_fieldSite] = (array) $taskInfo[$this->_fieldSite];

			$fieldCode = sprintf( '<select name="tx_scheduler[%1$s][]" id="%1$s" multiple="multiple" size="10" />', $this->_fieldSite );
			$fieldCode .= $this->_getSiteOptions( $this->_getAvailableSites(), $taskInfo[$this->_fieldSite], 0 );
			$fieldCode .= '</select>';

			$additionalFields[$this->_fieldSite] = array(
				'code'     => $fieldCode,
				'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:catalog.label.sitecode',
				'cshKey'   => 'xMOD_tx_arcavias',
				'cshLabel' => $this->_fieldSite
			);
		}
		catch( Exception $e )
		{
			error_log( 'Error in catalog scheduler: ' . $e->getMessage() );
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
			$context = $this->_getContext();
			$manager = MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'site' );

			$search = $manager->createSearch( true );
			$expr = array(
				$search->compare( '==', 'locale.site.code', $submittedData[$this->_fieldSite] ),
				$search->getConditions(),
			);
			$search->setConditions( $search->combine( '&&', $expr ) );

			if( count( $manager->searchItems( $search ) ) === count( $submittedData[$this->_fieldSite] ) ) {
				return true;
			}

			$message = $GLOBALS['LANG']->sL('LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:catalog.error.sitecode');
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

		$search = $manager->createSearch( true );
		$expr = array(
			$search->getConditions(),
			$search->compare( '==', 'locale.site.level', 0 ),
		);
		$search->setConditions( $search->combine( '&&', $expr ) );
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

		foreach( $siteItems AS $item )
		{
			$active = ( in_array( $item->getCode(), $selected ) ? 'selected="selected"' : '' );
			$string = '<option value="%1$s" %2$s>%3$s</option>';
			$html .= sprintf( $string, $item->getCode(), $active, $prefix . $item->getLabel() );

			$html .= $this->_getSiteOptions( $item->getChildren(), $selected, $level+1 );
		}

		return $html;
	}
}
