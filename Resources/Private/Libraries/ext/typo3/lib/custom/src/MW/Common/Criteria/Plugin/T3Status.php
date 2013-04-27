<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Criteria plugin for TYPO3 status/disable.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Plugin_T3Status implements MW_Common_Criteria_Plugin_Interface
{
	/**
	 * Translates the MShop value into its TYPO3 equivalent.
	 *
	 * @param integer $value Status value
	 * @return integer Value for TYPO3 "disabled" field
	 */
	public function translate( $value )
	{
		return ( $value ? 0 : 1 );
	}


	/**
	 * Reverses the translation from the TYPO3 value to the MShop constant.
	 *
	 * @param integer $value Value for TYPO3 "disabled" field
	 * @return integer Status value
	 */
	public function reverse( $value )
	{
		return ( $value ? 0 : 1 );
	}
}
