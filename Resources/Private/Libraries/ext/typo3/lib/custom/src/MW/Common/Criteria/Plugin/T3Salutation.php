<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Criteria plugin for TYPO3 salutation/gender.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Plugin_T3Salutation implements MW_Common_Criteria_Plugin_Interface
{
	/**
	 * Translates the MShop value into its TYPO3 equivalent.
	 *
	 * @param string $value Address constant from MShop_Common_Item_Address_Abstract
	 * @return string TYPO3 gender value or 99 if nothing matches
	 */
	public function translate( $value )
	{
		switch( $value )
		{
			case MShop_Common_Item_Address_Abstract::SALUTATION_MRS:
			case MShop_Common_Item_Address_Abstract::SALUTATION_MISS:
				return 0;
			case MShop_Common_Item_Address_Abstract::SALUTATION_MR:
				return 1;
		}

		return 99;
	}


	/**
	 * Reverses the translation from the TYPO3 value to the MShop constant.
	 *
	 * @param string $value TYPO3 value or empty if not set
	 * @return string Address constant from MShop_Common_Item_Address_Abstract
	 */
	public function reverse( $value )
	{
		switch( $value )
		{
			case 0:
				return MShop_Common_Item_Address_Abstract::SALUTATION_MRS;
			case 1:
				return MShop_Common_Item_Address_Abstract::SALUTATION_MR;
		}

		return MShop_Common_Item_Address_Abstract::SALUTATION_UNKNOWN;
	}
}
