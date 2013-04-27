<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/license
 * @package MShop
 * @subpackage Common
 */


/**
 * Criteria plugin for TYPO3 date values.
 *
 * @package MW
 * @subpackage Common
 */
class MW_Common_Criteria_Plugin_T3Date implements MW_Common_Criteria_Plugin_Interface
{
	/**
	 * Translates ISO dates into seconds relative to the epoch.
	 *
	 * @param string|null $value ISO date string (YYYY-MM-DD)
	 * @return integer Seconds relative to the epoch
	 */
	public function translate( $value )
	{
		return ( $value !== null ? strtotime( $value ) : 0 );
	}


	/**
	 * Reverses the translation from seconds relative to the epoch to the ISO date string (YYYY-MM-DD).
	 *
	 * @param integer $value Seconds relative to the epoch
	 * @return string|null ISO date string (YYYY-MM-DD)
	 */
	public function reverse( $value )
	{
		return ( $value != 0 ? date( 'Y-m-d', $value ) : null );
	}
}
