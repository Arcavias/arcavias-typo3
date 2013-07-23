<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias catalog controller.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Controller_CatalogController extends Tx_Arcavias_Controller_Abstract
{
	/**
	 * Renders the catalog filter section.
	 */
	public function filterAction()
	{
		$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->_getContext(), $templatePaths );

		return $this->_getClientOutput( $client );
	}


	/**
	 * Renders the catalog search filter section.
	 */
	public function filtersearchAction()
	{
		$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
		$client = Client_Html_Catalog_Filter_Factory::createClient( $this->_getContext(), $templatePaths );
		$client = $client->getSubClient( 'search' );

		return $this->_getClientOutput( $client );
	}


	/**
	 * Renders the catalog stage section.
	 */
	public function stageAction()
	{
		$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
		$client = Client_Html_Catalog_Stage_Factory::createClient( $this->_getContext(), $templatePaths );

		return $this->_getClientOutput( $client );
	}


	/**
	 * Renders the catalog list section.
	 */
	public function listAction()
	{
		if( is_object( $GLOBALS['TSFE'] ) && isset( $GLOBALS['TSFE']->config['config'] ) ) {
			$GLOBALS['TSFE']->config['config']['noPageTitle'] = 2;
		}

		$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
		$client = Client_Html_Catalog_List_Factory::createClient( $this->_getContext(), $templatePaths );

		return $this->_getClientOutput( $client );
	}


	/**
	 * Renders a list of product names in JSON format.
	 */
	public function listsimpleAction()
	{
		$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
		$client = Client_Html_Catalog_List_Factory::createClient( $this->_getContext(), $templatePaths, 'Simple' );

		return $this->_getClientOutput( $client );
	}


	/**
	 * Renders the catalog detail section.
	 */
	public function detailAction()
	{
		if( is_object( $GLOBALS['TSFE'] ) && isset( $GLOBALS['TSFE']->config['config'] ) ) {
			$GLOBALS['TSFE']->config['config']['noPageTitle'] = 2;
		}

		$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
		$client = Client_Html_Catalog_Detail_Factory::createClient( $this->_getContext(), $templatePaths );

		return $this->_getClientOutput( $client );
	}
}
