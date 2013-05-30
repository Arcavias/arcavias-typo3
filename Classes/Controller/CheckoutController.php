<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias checkout controller.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Controller_CheckoutController extends Tx_Arcavias_Controller_Abstract
{
	/**
	 * Processes requests and renders the checkout process.
	 */
	public function indexAction()
	{
		try
		{
			$templatePaths = $this->_getMShop()->getCustomPaths( 'client/html' );
			$client = Client_Html_Checkout_Standard_Factory::createClient( $this->_getContext(), $templatePaths );

			return $this->_getClientOutput( $client );
		}
		catch( Exception $e )
		{
			$this->flashMessageContainer->add(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			);
		}
	}


	/**
	 * Processes requests and renders the checkout confirmation.
	 */
	public function confirmAction()
	{
		try
		{
			$templatePaths = $this->_getMShop()->getCustomPaths( 'client/html' );
			$client = Client_Html_Checkout_Confirm_Factory::createClient( $this->_getContext(), $templatePaths );

			$view = $this->_createView();
			$helper = new MW_View_Helper_Parameter_Default( $view, $_REQUEST );
			$view->addHelper( 'param', $helper );

			$client->setView( $view );
			$client->process();

			$this->response->addAdditionalHeaderData( $client->getHeader() );

			return $client->getBody();
		}
		catch( Exception $e )
		{
			$this->flashMessageContainer->add(
				'An error occured. Please go back to the previous page and try again',
				'Error',
				t3lib_Flashmessage::ERROR
			);
		}
	}


	/**
	 * Processes update requests from payment service providers.
	 */
	public function updateAction()
	{
		try
		{
			$templatePaths = $this->_getMShop()->getCustomPaths( 'client/html' );
			$client = Client_Html_Checkout_Update_Factory::createClient( $this->_getContext(), $templatePaths );

			$view = $this->_createView();
			$helper = new MW_View_Helper_Parameter_Default( $view, $_REQUEST );
			$view->addHelper( 'param', $helper );

			$client->setView( $view );
			$client->process();

			$this->response->addAdditionalHeaderData( $client->getHeader() );

			return $client->getBody();
		}
		catch( Exception $e )
		{
			return 'Error: ' . $e->getMessage();
		}
	}
}
