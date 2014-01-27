<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


namespace Arcavias\Arcavias\Scheduler\Provider;


/**
 * Additional field provider for Arcavias scheduler.
 *
 * @package TYPO3_Arcavias
 */
class Typo6
	extends \Tx_Arcavias_Scheduler_Provider_Abstract
	implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface
{
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
	public function getAdditionalFields( array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject )
	{
		try {
			return $this->_getAdditionalFields( $taskInfo, $task, $parentObject );
		} catch( \Exception $e ) {
			$parentObject->addMessage( $e->getMessage(), \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}

		return array();
	}


	/**
	 * Store fields.
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param array $submittedData Array containing the data submitted by the user
	 * @param tx_scheduler_Task	$task Reference to the current task object
	 */
	public function saveAdditionalFields( array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task )
	{
		$this->_saveAdditionalFields( $submittedData, $task );
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
	public function validateAdditionalFields( array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject )
	{
		try {
			return $this->_validateAdditionalFields( $submittedData, $parentObject );
		} catch( \Exception $e ) {
			$parentObject->addMessage( $e->getMessage(), \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR );
		}

		return false;
	}
}
