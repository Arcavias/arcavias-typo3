<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Common methods for Arcavias' e-mail additional field providers.
 *
 * @package TYPO3_Arcavias
 */
abstract class Tx_Arcavias_Scheduler_Provider_Email
 extends Tx_Arcavias_Scheduler_Provider_Abstract
{
	private $_fieldSenderFrom = 'arcavias_sender_from';
	private $_fieldSenderEmail = 'arcavias_sender_email';
	private $_fieldReplyEmail = 'arcavias_reply_email';


	/**
	 * Fields generation.
	 * This method is used to define new fields for adding or editing a task
	 * In this case, it adds a page ID field
	 *
	 * @param array $taskInfo Reference to the array containing the info used in the add/edit form
	 * @param object $task When editing, reference to the current task object. Null when adding.
	 * @param object $parentObject Reference to the calling object (Scheduler's BE module)
	 * @return array Array containg all the information pertaining to the additional fields
	 *		The array is multidimensional, keyed to the task class name and each field's id
	 *		For each field it provides an associative sub-array with the following:
	 *			['code']		=> The HTML code for the field
	 *			['label']		=> The label of the field (possibly localized)
	 *			['cshKey']		=> The CSH key for the field
	 *			['cshLabel']	=> The code of the CSH label
	 */
	protected function _getAdditionalFields( array &$taskInfo, $task, $parentObject )
	{
		$additionalFields = array();


		// In case of editing a task, set to the internal value if data wasn't already submitted
		if( empty( $taskInfo[$this->_fieldSenderFrom] ) && $parentObject->CMD === 'edit' ) {
			$taskInfo[$this->_fieldSenderFrom] = $task->{$this->_fieldSenderFrom};
		}

		$taskInfo[$this->_fieldSenderFrom] = htmlspecialchars( $taskInfo[$this->_fieldSenderFrom], ENT_QUOTES, 'UTF-8' );

		$fieldStr = '<input name="tx_scheduler[%1$s]" id="%1$s" value="%2$s">';
		$fieldCode = sprintf( $fieldStr, $this->_fieldSenderFrom, $taskInfo[$this->_fieldSenderFrom] );

		$additionalFields[$this->_fieldSenderFrom] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:email.label.from-name',
			'cshKey'   => 'xMOD_tx_arcavias',
			'cshLabel' => $this->_fieldSenderFrom
		);


		// In case of editing a task, set to the internal value if data wasn't already submitted
		if( empty( $taskInfo[$this->_fieldSenderEmail] ) && $parentObject->CMD === 'edit' ) {
			$taskInfo[$this->_fieldSenderEmail] = $task->{$this->_fieldSenderEmail};
		}

		$taskInfo[$this->_fieldSenderEmail] = htmlspecialchars( $taskInfo[$this->_fieldSenderEmail], ENT_QUOTES, 'UTF-8' );

		$fieldStr = '<input name="tx_scheduler[%1$s]" id="%1$s" value="%2$s">';
		$fieldCode = sprintf( $fieldStr, $this->_fieldSenderEmail, $taskInfo[$this->_fieldSenderEmail] );

		$additionalFields[$this->_fieldSenderEmail] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:email.label.from-email',
			'cshKey'   => 'xMOD_tx_arcavias',
			'cshLabel' => $this->_fieldSenderEmail
		);


		// In case of editing a task, set to the internal value if data wasn't already submitted
		if( empty( $taskInfo[$this->_fieldReplyEmail] ) && $parentObject->CMD === 'edit' ) {
			$taskInfo[$this->_fieldReplyEmail] = $task->{$this->_fieldReplyEmail};
		}

		$taskInfo[$this->_fieldReplyEmail] = htmlspecialchars( $taskInfo[$this->_fieldReplyEmail], ENT_QUOTES, 'UTF-8' );

		$fieldStr = '<input name="tx_scheduler[%1$s]" id="%1$s" value="%2$s">';
		$fieldCode = sprintf( $fieldStr, $this->_fieldReplyEmail, $taskInfo[$this->_fieldReplyEmail] );

		$additionalFields[$this->_fieldReplyEmail] = array(
			'code'     => $fieldCode,
			'label'    => 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:email.label.reply-email',
			'cshKey'   => 'xMOD_tx_arcavias',
			'cshLabel' => $this->_fieldReplyEmail
		);


		$additionalFields += parent::_getAdditionalFields( $taskInfo, $task, $parentObject );

		return $additionalFields;
	}


	/**
	 * Store fields.
	 * This method is used to save any additional input into the current task object
	 * if the task class matches
	 *
	 * @param array $submittedData Array containing the data submitted by the user
	 * @param object $task Reference to the current task object
	 */
	protected function _saveAdditionalFields( array $submittedData, $task )
	{
		parent::_saveAdditionalFields( $submittedData, $task );

		$task->{$this->_fieldSenderFrom} = $submittedData[$this->_fieldSenderFrom];
		$task->{$this->_fieldSenderEmail} = $submittedData[$this->_fieldSenderEmail];
		$task->{$this->_fieldReplyEmail} = $submittedData[$this->_fieldReplyEmail];
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
	protected function _validateAdditionalFields( array &$submittedData, $parentObject )
	{
		if( preg_match( '/^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$/', $submittedData[$this->_fieldSenderEmail] ) !== 1 ) {
			throw new Exception( $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:email.error.from-email.invalid' ) );
		}

		if( $submittedData[$this->_fieldReplyEmail] != ''
			&& preg_match( '/^.+@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)*$/', $submittedData[$this->_fieldReplyEmail] ) !== 1
		) {
			throw new Exception( $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:email.error.reply-email.invalid' ) );
		}

		parent::_validateAdditionalFields( $submittedData, $parentObject );

		return true;
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
		$arcavias = Tx_Arcavias_Base::getArcavias();
		$context = Tx_Arcavias_Scheduler_Base::getContext();
		$cntlPaths = $arcavias->getCustomPaths( 'controller/jobs' );

		$controllers = Controller_Jobs_Factory::getControllers( $context, $arcavias, $cntlPaths );

		foreach( $controllers as $name => $controller )
		{
			if( strncmp( $name, 'order/email/', 12 ) === 0 )
			{
				$active = ( in_array( $name, $selected ) ? 'selected="selected"' : '' );
				$cntl = htmlspecialchars( $controller->getName(), ENT_QUOTES, 'UTF-8' );
				$name = htmlspecialchars( $name, ENT_QUOTES, 'UTF-8' );

				$html .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', $name, $active, $cntl );
			}
		}

		return $html;
	}
}
