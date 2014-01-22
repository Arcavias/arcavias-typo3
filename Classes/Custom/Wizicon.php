<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Class that adds the wizard icon.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_custom_wizicon
{
	/**
	 * Adds the wizard icon
	 *
	 * @param array Input array with wizard items for plugins
	 * @return array Modified input array, having the item for Arcavias added.
	 */
	public function proc( $wizardItems )
	{
		$extPath = t3lib_extMgm::extPath( 'arcavias' );
		$file = $extPath . 'Resources/Private/Language/Extension.xml';
		$xml = t3lib_div::readLLfile( $file, $GLOBALS['LANG']->lang );

		$wizardItems['plugins_tx_arcavias'] = array(
			'icon' => $extPath . 'Resources/Public/images/arcavias-wizicon.gif',
			'title' => $GLOBALS['LANG']->getLLL( 'ext-wizard-title', $xml ),
			'description' => $GLOBALS['LANG']->getLLL( 'ext-wizard-description', $xml ),
			'params' => '&defVals[tt_content][CType]=list'
		);

		return $wizardItems;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/arcavias/Classes/Custom/Wizicon.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/arcavias/Classes/Custom/Wizicon.php']);
}
