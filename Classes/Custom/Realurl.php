<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias RealURL configuraiton.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_custom_realurl
{
	/**
	 * Generates additional RealURL configuration and merges it with provided configuration
	 *
	 * @param array $params Default configuration
	 * @param tx_realurl_autoconfgen $pObj Parent object
	 * @return array Updated configuration
	 */
	function addAutoConfig( $params, &$pObj )
	{
		return array_merge_recursive( $params['config'], array(
			'postVarSets' => array(
				'_DEFAULT' => array(
					'arcavias' => array(
						array(
							'GETvar' => 'arc[controller]',
							'noMatch' => 'bypass',
						),
						array(
							'GETvar' => 'arc[action]',
							'noMatch' => 'bypass',
						),
					),
					'cat' => array(
						array( 'GETvar' => 'arc[f-catalog-id]' ),
					),
					'page' => array(
						array( 'GETvar' => 'arc[l-page]' ),
					),
					'sort' => array(
						array(
							'GETvar' => 'arc[l-sort]',
							'valueMap' => array(
								'name' => 'name',
								'price' => 'price',
							),
							'noMatch' => 'bypass',
						),
					),
					'search' => array(
						array( 'GETvar' => 'arc[f-search-text]' ),
					),
					'prod' => array(
						array( 'GETvar' => 'arc[l-product-id]' ),
					),
					'---' => array(
						array( 'GETvar' => 'arc[a-name]' ),
					),
					'bt' => array(
						array( 'GETvar' => 'arc[b-action]' ),
						array( 'GETvar' => 'arc[b-position]' ),
						array( 'GETvar' => 'arc[b-quantity]' ),
					),
					'co' => array(
						array( 'GETvar' => 'arc[c-step]' ),
					),
				),
			),
		) );
	}
}
