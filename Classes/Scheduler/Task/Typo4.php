<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias scheduler.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Scheduler_Task_Typo4
	extends tx_scheduler_Task
{
	private $_fieldSite = 'arcavias_sitecode';
	private $_fieldController = 'arcavias_controller';
	private $_fieldTSconfig = 'arcavias_config';


	/**
	 * Executes the configured tasks.
	 *
	 * @return boolean True if success, false if not
	 * @throws Exception If an error occurs
	 */
	public function execute()
	{
		$langid = 'en';
		if( isset( $GLOBALS['BE_USER']->user['lang'] ) && $GLOBALS['BE_USER']->user['lang'] != '' ) {
			$langid = $GLOBALS['BE_USER']->user['lang'];
		}

		$sitecodes = (array) $this->{$this->_fieldSite};
		$controllers = (array) $this->{$this->_fieldController};
		$tsconfig = $this->{$this->_fieldTSconfig};

		return Tx_Arcavias_Scheduler_Base::execute( $sitecodes, $controllers, $tsconfig, $langid );
	}
}
