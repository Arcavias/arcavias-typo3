<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Common
 * @version $Id: Typo3.php 14246 2011-12-09 12:25:12Z nsendetzky $
 */


/**
 * Criteria plugin for TYPO3 addresses.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Manager_Address_Plugin_Typo3 implements MW_Common_Criteria_Plugin_Interface
{
	public function translate( $value )
	{
		switch( $value )
		{
			case MShop_Common_Item_Address_Abstract::SALUTATION_COMPANY:
				return '';
			case MShop_Common_Item_Address_Abstract::SALUTATION_MRS:
			case MShop_Common_Item_Address_Abstract::SALUTATION_MISS:
				return 'f';
			case MShop_Common_Item_Address_Abstract::SALUTATION_MR:
				return 'm';
		}
	}
}
