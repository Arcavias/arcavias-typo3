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
		try
		{
			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Catalog_Filter_Factory::createClient( $this->_getContext(), $templatePaths );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			t3lib_FlashMessageQueue::addMessage( new t3lib_FlashMessage(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			) );
		}
	}


	/**
	 * Renders the catalog search filter section.
	 */
	public function filtersearchAction()
	{
		try
		{
			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Catalog_Filter_Factory::createClient( $this->_getContext(), $templatePaths );
			$client = $client->getSubClient( 'search' );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			t3lib_FlashMessageQueue::addMessage( new t3lib_FlashMessage(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			) );
		}
	}


	/**
	 * Renders the catalog stage section.
	 */
	public function stageAction()
	{
		try
		{
			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Catalog_Stage_Factory::createClient( $this->_getContext(), $templatePaths );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			t3lib_FlashMessageQueue::addMessage( new t3lib_FlashMessage(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			) );
		}
	}


	/**
	 * Renders the catalog stock section.
	 */
	public function stockAction()
	{
		try
		{
			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Catalog_Stock_Factory::createClient( $this->_getContext(), $templatePaths );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			t3lib_FlashMessageQueue::addMessage( new t3lib_FlashMessage(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			) );
		}
	}


	/**
	 * Renders the catalog list section.
	 */
	public function listAction()
	{
		try
		{
			if( is_object( $GLOBALS['TSFE'] ) && isset( $GLOBALS['TSFE']->config['config'] ) ) {
				$GLOBALS['TSFE']->config['config']['noPageTitle'] = 2;
			}

			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Catalog_List_Factory::createClient( $this->_getContext(), $templatePaths );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			t3lib_FlashMessageQueue::addMessage( new t3lib_FlashMessage(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			) );
		}
	}


	/**
	 * Renders a list of product names in JSON format.
	 */
	public function listsimpleAction()
	{
		try
		{
			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Catalog_List_Factory::createClient( $this->_getContext(), $templatePaths, 'Simple' );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			t3lib_FlashMessageQueue::addMessage( new t3lib_FlashMessage(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			) );
		}
	}


	/**
	 * Renders the catalog detail section.
	 */
	public function detailAction()
	{
		try
		{
			if( is_object( $GLOBALS['TSFE'] ) && isset( $GLOBALS['TSFE']->config['config'] ) ) {
				$GLOBALS['TSFE']->config['config']['noPageTitle'] = 2;
			}

			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Catalog_Detail_Factory::createClient( $this->_getContext(), $templatePaths );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			t3lib_FlashMessageQueue::addMessage( new t3lib_FlashMessage(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			) );
		}
	}
}
