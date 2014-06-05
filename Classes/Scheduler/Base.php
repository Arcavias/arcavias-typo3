<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias common scheduler class.
 *
 * @package TYPO3_Arcavias
 */
class Tx_Arcavias_Scheduler_Base
{
	static private $_context;


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @param MW_Config_Interface $config Configuration object
	 * @return MW_View_Interface View object
	 */
	public static function createView( MW_Config_Interface $config )
	{
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );
		$helper = new MW_View_Helper_Number_Default( $view, $sepDec, $sep1000 );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_Url_None( $view );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		return $view;
	}


	/**
	 * Executes the jobs.
	 *
	 * @param array $sitecodes List of site codes
	 * @param array $controllers List of controller names
	 * @param string $tsconfig TypoScript configuration string
	 * @param string $langid Two letter ISO language code of the backend user
	 * @throws Controller_Jobs_Exception If a job can't be executed
	 * @throws MShop_Exception If an error in a manager occurs
	 * @throws MW_DB_Exception If a database error occurs
	 */
	public static function execute( array $sitecodes, array $controllers, $tsconfig, $langid )
	{
		$conf = Tx_Arcavias_Base::parseTS( $tsconfig );
		$context = self::getContext( $conf );
		$arcavias = Tx_Arcavias_Base::getArcavias();

		$manager = MShop_Locale_Manager_Factory::createManager( $context );

		foreach( $sitecodes as $sitecode )
		{
			$localeItem = $manager->bootstrap( $sitecode, $langid, '', false );
			$context->setLocale( $localeItem );

			foreach( (array) $controllers as $name ) {
				Controller_Jobs_Factory::createController( $context, $arcavias, $name )->run();
			}
		}

		return true;
	}


	/**
	 * Returns the current context.
	 *
	 * @param array Multi-dimensional associative list of key/value pairs
	 * @return MShop_Context_Item_Interface Context object
	 */
	public static function getContext( array $localConf = array() )
	{
		if( self::$_context === null )
		{
			// Important! Sets include paths
			$arcavias = Tx_Arcavias_Base::getArcavias();
			$context = new MShop_Context_Item_Default();


			$conf = Tx_Arcavias_Base::getConfig( $localConf );
			$context->setConfig( $conf );

			$dbm = new MW_DB_Manager_PDO( $conf );
			$context->setDatabaseManager( $dbm );

			$cache = new MW_Cache_None();
			$context->setCache( $cache );

			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			$mail = new MW_Mail_Typo3( t3lib_div::makeInstance( 't3lib_mail_Message' ) );
			$context->setMail( $mail );

			$i18n = self::_createI18n( $context, $arcavias->getI18nPaths() );
			$context->setI18n( $i18n );

			$view = Tx_Arcavias_Scheduler_Base::createView( $conf );
			$context->setView( $view );

			$context->setEditor( 'scheduler' );

			$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
			$localeItem = $localeManager->createItem();
			$localeItem->setLanguageId( 'en' );
			$context->setLocale( $localeItem );


			self::$_context = $context;
		}

		return self::$_context;
	}


	/**
	 * Creates new translation objects.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array List of paths to the i18n files
	 * @return array List of translation objects implementing MW_Translation_Interface
	 */
	protected static function _createI18n( MShop_Context_Item_Interface $context, array $i18nPaths )
	{
		$list = array();
		$config = $context->getConfig();
		$langManager = MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'language' );

		foreach( $langManager->searchItems( $langManager->createSearch( true ) ) as $id => $langItem )
		{
			$i18n = new MW_Translation_Zend( $i18nPaths, 'gettext', $id, array( 'disableNotices' => true ) );

			if( ( $entries = $config->get( 'i18n/' . $id ) ) !== null )
			{
				$translations = array();

				foreach( (array) $entries as $entry )
				{
					if( isset( $entry['domain'] ) && isset( $entry['string'] ) && isset( $entry['trans'] ) )
					{
						$string = str_replace( '\\n', "\n", $entry['string'] );
						$trans = array();

						foreach( (array) $entry['trans'] as $tx ) {
							$trans[] = str_replace( '\\n', "\n", $tx );
						}

						$translations[$entry['domain']][$string] = $trans;
					}
				}

				$i18n = new MW_Translation_Decorator_Memory( $i18n, $translations );
			}

			$list[$id] = $i18n;
		}

		return $list;
	}
}
