<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias locale controller.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Controller_LocaleController extends Tx_Arcavias_Controller_Abstract
{
	/**
	 * Processes requests and renders the locale selector.
	 */
	public function selectAction()
	{
		try
		{
			$templatePaths = Tx_Arcavias_Base::getArcavias()->getCustomPaths( 'client/html' );
			$client = Client_Html_Locale_Select_Factory::createClient( $this->_getContext(), $templatePaths );

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
