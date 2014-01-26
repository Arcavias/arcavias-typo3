<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias account controller.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Controller_AccountController extends Tx_Arcavias_Controller_Abstract
{
	/**
	 * Renders the account history.
	 */
	public function historyAction()
	{
		try
		{
			$templatePaths = $this->_getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Account_History_Factory::createClient( $this->_getContext(), $templatePaths );

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
