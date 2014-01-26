<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


/**
 * Arcavias abstract scheduler.
 *
 * @package TYPO3_Arcavias
 */
abstract class Tx_Arcavias_Scheduler_Base
{
	static private $_arcavias;
	static private $_context;


	/**
	 * Creates new translation objects.
	 *
	 * @param MShop_Context_Item_Interface $context Context object
	 * @param array List of paths to the i18n files
	 * @return array List of translation objects implementing MW_Translation_Interface
	 */
	public static function createI18n( MShop_Context_Item_Interface $context, array $i18nPaths )
	{
		$list = array();
		$langManager = MShop_Locale_Manager_Factory::createManager( $context )->getSubManager( 'language' );

		foreach( $langManager->searchItems( $langManager->createSearch( true ) ) as $id => $langItem )
		{
			$i18n = new MW_Translation_Zend( $i18nPaths, 'gettext', $id, array( 'disableNotices' => true ) );
			$list[$id] = $i18n;
		}

		return $list;
	}


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
		$conf = self::parseTS( $tsconfig );
		$context = self::getContext( $conf );
		$arcavias = self::getArcavias();

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
			$ds = DIRECTORY_SEPARATOR;

			// Important! Sets include paths
			$arcavias = Tx_Arcavias_Scheduler_Base::getArcavias();
			$context = new MShop_Context_Item_Default();


			$configPaths = $arcavias->getConfigPaths( 'mysql' );

			// Hook for processing extension directories
			if( is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['confDirs'] ) )
			{
				foreach( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['confDirs'] as $dir )
				{
					$absPath = t3lib_div::getFileAbsFileName( $dir );
					if( !empty( $absPath ) ) {
						$configPaths[] = $absPath;
					}
				}
			}

			$conf = new MW_Config_Array( array(), $configPaths );
			$conf = new MW_Config_Decorator_Memory( $conf, $localConf );
			$context->setConfig( $conf );

			$dbm = new MW_DB_Manager_PDO( $conf );
			$context->setDatabaseManager( $dbm );

			$cache = new MW_Cache_None();
			$context->setCache( $cache );

			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			$mail = new MW_Mail_Typo3( t3lib_div::makeInstance( 't3lib_mail_Message' ) );
			$context->setMail( $mail );

			$i18n = Tx_Arcavias_Scheduler_Base::createI18n( $context, $arcavias->getI18nPaths() );
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
	 * Returns the Arcavias object.
	 *
	 * @return Arcavias Arcavias object
	 */
	public static function getArcavias()
	{
		if( self::$_arcavias === null )
		{
			$ds = DIRECTORY_SEPARATOR;
			$libPath = t3lib_extMgm::extPath( 'arcavias' ) . 'vendor' . $ds . 'arcavias' . $ds . 'arcavias-core';

			// Hook for processing extension directories
			$extDirs = array();
			if( is_array( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['extDirs'] ) )
			{
				foreach( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['extDirs'] as $dir )
				{
					$absPath = t3lib_div::getFileAbsFileName( $dir );
					if( !empty( $absPath ) ) {
						$extDirs[] = $absPath;
					}
				}
			}

			self::$_arcavias = new Arcavias( $extDirs, false, $libPath );
		}

		return self::$_arcavias;
	}


	/**
	 * Parses TypoScript configuration string.
	 *
	 * @param string $tsString TypoScript string
	 * @return array Mulit-dimensional, associative list of key/value pairs
	 * @throws Exception If parsing the configuration string fails
	 */
	public static function parseTS( $tsString )
	{
		$parser = t3lib_div::makeInstance( 't3lib_tsparser' );
		$parser->parse( $tsString );

		if( !empty( $parser->errors ) )
		{
			$msg = $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Scheduler.xml:default.error.tsconfig.invalid' );
			throw new Exception( $msg );
		}

		return self::_convertTypoScriptArrayToPlainArray( $parser->setup );
	}


	/**
	 * Removes dots from config keys (copied from Extbase TypoScriptService class available since TYPO3 6.0)
	 *
	 * @param array $typoScriptArray TypoScript configuration array
	 * @return array Multi-dimensional, associative list of key/value pairs without dots in keys
	 */
	protected static function _convertTypoScriptArrayToPlainArray(array $typoScriptArray)
	{
		foreach ($typoScriptArray as $key => &$value) {
			if (substr($key, -1) === '.') {
				$keyWithoutDot = substr($key, 0, -1);
				$hasNodeWithoutDot = array_key_exists($keyWithoutDot, $typoScriptArray);
				$typoScriptNodeValue = $hasNodeWithoutDot ? $typoScriptArray[$keyWithoutDot] : NULL;
				if (is_array($value)) {
					$typoScriptArray[$keyWithoutDot] = $this->_convertTypoScriptArrayToPlainArray($value);
					if (!is_null($typoScriptNodeValue)) {
						$typoScriptArray[$keyWithoutDot]['_typoScriptNodeValue'] = $typoScriptNodeValue;
					}
					unset($typoScriptArray[$key]);
				} else {
					$typoScriptArray[$keyWithoutDot] = NULL;
				}
			}
		}
		return $typoScriptArray;
	}
}
