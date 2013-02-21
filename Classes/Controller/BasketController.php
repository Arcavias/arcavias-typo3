<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias basket controller.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Controller_BasketController extends Tx_Arcavias_Controller_Abstract
{
	/**
	 * Processes requests and renders the basket.
	 */
	public function indexAction()
	{
		try
		{
			$templatePaths = $this->_getMShop()->getCustomPaths( 'client/html' );
			$client = Client_Html_Basket_Standard_Factory::createClient( $this->_getContext(), $templatePaths );

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
	 * Renders a small basket.
	 */
	public function smallAction()
	{
		try
		{
			$templatePaths = $this->_getMShop()->getCustomPaths( 'client/html' );
			$client = Client_Html_Basket_Mini_Factory::createClient( $this->_getContext(), $templatePaths );

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
}
