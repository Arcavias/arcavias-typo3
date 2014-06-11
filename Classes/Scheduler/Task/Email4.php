<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias e-mail scheduler.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Scheduler_Task_Email4
	extends tx_scheduler_Task
{
	private $_fieldSite = 'arcavias_sitecode';
	private $_fieldController = 'arcavias_controller';
	private $_fieldTSconfig = 'arcavias_config';
	private $_fieldSenderFrom = 'arcavias_sender_from';
	private $_fieldSenderEmail = 'arcavias_sender_email';
	private $_fieldReplyEmail = 'arcavias_reply_email';
	private $_fieldPageDetail = 'arcavias_pageid_detail';
	private $_fieldContentBaseurl = 'arcavias_content_baseurl';


	/**
	 * Executes the configured tasks.
	 *
	 * @return boolean True if success, false if not
	 * @throws Exception If an error occurs
	 */
	public function execute()
	{
		$langid = 'en';
		if( isset( $GLOBALS['BE_USER']->user['lang'] ) && $GLOBALS['BE_USER']->user['lang'] != '' ) {
			$langid = $GLOBALS['BE_USER']->user['lang'];
		}


		$conf = Tx_Arcavias_Base::parseTS( $this->{$this->_fieldTSconfig} );

		if( $this->{$this->_fieldSenderFrom} != '' ) {
			$conf['client']['html']['email']['from-name'] = $this->{$this->_fieldSenderFrom};
		}

		if( $this->{$this->_fieldSenderEmail} != '' ) {
			$conf['client']['html']['email']['from-email'] = $this->{$this->_fieldSenderEmail};
		}

		if( $this->{$this->_fieldReplyEmail} != '' ) {
			$conf['client']['html']['email']['reply-email'] = $this->{$this->_fieldReplyEmail};
		}

		if( $this->{$this->_fieldContentBaseurl} != '' ) {
			$conf['client']['html']['common']['content']['baseurl'] = $this->{$this->_fieldContentBaseurl};
		}

		if( $this->{$this->_fieldPageDetail} != '' ) {
			$conf['client']['html']['catalog']['detail']['url']['target'] = $this->{$this->_fieldPageDetail};
		}


		$context = Tx_Arcavias_Scheduler_Base::getContext( $conf );
		$arcavias = Tx_Arcavias_Base::getArcavias();

		$manager = MShop_Locale_Manager_Factory::createManager( $context );

		foreach( (array) $this->{$this->_fieldSite} as $sitecode )
		{
			$localeItem = $manager->bootstrap( $sitecode, $langid, '', false );
			$context->setLocale( $localeItem );

			foreach( (array) $this->{$this->_fieldController} as $name ) {
				Controller_Jobs_Factory::createController( $context, $arcavias, $name )->run();
			}
		}

		return true;
	}
}
