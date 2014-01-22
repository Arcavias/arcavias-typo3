<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


/**
 * Abstract class with common functionality for all controllers.
 *
 * @package TYPO3_Arcavias
 */
abstract class Tx_Arcavias_Controller_Abstract extends Tx_Extbase_MVC_Controller_ActionController
{
	static private $_locale;
	static private $_config;
	static private $_context;
	static private $_arcavias;
	static private $_extConfig;
	static private $_i18n = array();


	/**
	 * Initializes the object before the real action is called.
	 */
	protected function initializeAction()
	{
		$context = $this->_getContext();
		$config = $this->_getConfig();

		// Re-initialize the config object because the settings are different due to flexforms
		$context->setConfig( $config );

		if( !isset( self::$_locale ) )
		{
			$langid = 'en';
			if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
				$langid = $GLOBALS['TSFE']->config['config']['language'];
			}

			$sitecode = $config->get( 'mshop/locale/site', 'default' );
			$currency = $config->get( 'mshop/locale/currency', 'EUR' );

			$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
			$locale = $localeManager->bootstrap( $sitecode, $langid, $currency );

			self::$_locale = $locale;
		}

		$context->setLocale( self::$_locale );
		$context->setI18n( $this->_getI18n( array( self::$_locale->getLanguageId() ) ) );

		$this->uriBuilder->setArgumentPrefix( 'arc' );
	}


	/**
	 * Disables Fluid views for performance reasons.
	 *
	 * return Tx_Extbase_MVC_View_ViewInterface View object
	 */
	protected function resolveView()
	{
		return null;
	}


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @return MW_View_Interface View object
	 */
	protected function _createView()
	{
		$context = $this->_getContext();
		$config = $context->getConfig();

		$langid = $context->getLocale()->getLanguageId();
		$i18n = $this->_getI18n( array( $langid ) );

		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Typo3( $view, $this->uriBuilder );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Translate_Default( $view, $i18n[$langid] );
		$view->addHelper( 'translate', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, $this->request->getArguments() );
		$view->addHelper( 'param', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );
		$helper = new MW_View_Helper_Number_Default( $view, $sepDec, $sep1000 );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_FormParam_Default( $view, array( $this->uriBuilder->getArgumentPrefix() ) );
		$view->addHelper( 'formparam', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		return $view;
	}


	/**
	 * Returns the Arcavias object.
	 *
	 * @return Arcavias Arcavias object
	 */
	protected function _getArcavias()
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
	 * Creates a new configuration object.
	 *
	 * @return MW_Config_Interface Configuration object
	 */
	protected function _getConfig()
	{
		if( self::$_config === null )
		{
			$configPaths = $this->_getArcavias()->getConfigPaths( 'mysql' );

			// Hook for processing extension config directories
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

			if( function_exists( 'apc_store' ) === true && $this->_getExtConfig( 'useAPC', false ) == true ) {
				$conf = new MW_Config_Decorator_APC( $conf, $this->_getExtConfig( 'apcPrefix', 't3:' ) );
			}

			self::$_config = $conf;
		}

		$settings = (array) $this->settings;

		if( isset( $this->settings['typo3']['tsconfig'] ) )
		{
			$tsconfig = $this->_parseTS( $this->settings['typo3']['tsconfig'] );
			$settings = Tx_Extbase_Utility_Arrays::arrayMergeRecursiveOverrule( $settings, $tsconfig );
		}

		return new MW_Config_Decorator_Memory( self::$_config, $settings );
	}


	/**
	 * Returns the current context.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		if( self::$_context === null )
		{
			$context = new MShop_Context_Item_Default();


			$config = $this->_getConfig();
			$context->setConfig( $config );

			$dbm = new MW_DB_Manager_PDO( $config );
			$context->setDatabaseManager( $dbm );

			$cache = new MW_Cache_None();
			$context->setCache( $cache );

			if( isset( $GLOBALS['TSFE']->fe_user ) ) {
				$session = new MW_Session_Typo3( $GLOBALS['TSFE']->fe_user );
			} else {
				$session = new MW_Session_None();
			}
			$context->setSession( $session );

			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			if( TYPO3_MODE === 'FE' && $GLOBALS['TSFE']->loginUser == 1 )
			{
				$context->setEditor( $GLOBALS['TSFE']->fe_user->user['username'] );
				$context->setUserId( $GLOBALS['TSFE']->fe_user->user[$GLOBALS['TSFE']->fe_user->userid_column] );
			}


			self::$_context = $context;
		}

		return self::$_context;
	}


	/**
	 * Returns the extension configuration.
	 *
	 * @param string Name of the configuration setting
	 * @param mixed Value returned if no value in extension configuration was found
	 * @return mixed Value associated with the configuration setting
	 */
	protected function _getExtConfig( $name, $default = null )
	{
		if( self::$_extConfig === null )
		{
			if( ( $conf = unserialize( $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['arcavias'] ) ) === false ) {
				$conf = array();
			}

			self::$_extConfig = $conf;
		}

		if( isset( self::$_extConfig[$name] ) ) {
			return self::$_extConfig[$name];
		}

		return $default;
	}


	/**
	 * Creates new translation objects.
	 *
	 * @param array $langIds List of two letter ISO language IDs
	 * @return array List of translation objects implementing MW_Translation_Interface
	 */
	protected function _getI18n( array $languageIds )
	{
		$i18nPaths = $this->_getArcavias()->getI18nPaths();

		foreach( $languageIds as $langid )
		{
			if( !isset( self::$_i18n[$langid] ) )
			{
				$i18n = new MW_Translation_Zend( $i18nPaths, 'gettext', $langid, array( 'disableNotices' => true ) );

				if( function_exists( 'apc_store' ) === true && $this->_getExtConfig( 'useAPC', false ) == true )
				{
					$prefix = ( isset( $this->_settings['apc']['prefix'] ) ? $this->settings['apc']['prefix'] : '' );
					$i18n = new MW_Translation_Decorator_APC( $i18n, $prefix );
				}

				if( isset( $this->settings['i18n'][$langid] ) )
				{
					$translations = array();

					foreach( (array) $this->settings['i18n'][$langid] as $entry )
					{
						if( isset( $entry['domain'] ) && isset( $entry['string'] ) && isset( $entry['trans'] ) ) {
							$translations[$entry['domain']][$entry['string']] = (array) $entry['trans'];
						}
					}

					$i18n = new MW_Translation_Decorator_Memory( $i18n, $translations );
				}

				self::$_i18n[$langid] = $i18n;
			}
		}

		return self::$_i18n;
	}


	/**
	 * Returns the output of the client and adds the header.
	 *
	 * @param Client_Html_Interface $client Html client object
	 * @return string HTML code for inserting into the HTML body
	 */
	protected function _getClientOutput( Client_Html_Interface $client )
	{
		$client->setView( $this->_createView() );
		$client->process();

		$this->response->addAdditionalHeaderData( $client->getHeader() );

		return $client->getBody();
	}


	/**
	 * Parses TypoScript configuration string.
	 *
	 * @param string $tsString TypoScript string
	 * @return array Mulit-dimensional, associative list of key/value pairs
	 * @throws Exception If parsing the configuration string fails
	 */
	protected function _parseTS( $tsString )
	{
		$parser = t3lib_div::makeInstance( 't3lib_tsparser' );
		$parser->parse( $tsString );

		if( !empty( $parser->errors ) )
		{
			$msg = $GLOBALS['LANG']->sL( 'LLL:EXT:arcavias/Resources/Private/Language/Plugins.xml:default.error.tsconfig.invalid' );
			throw new Exception( $msg );
		}

		return $this->_convertTypoScriptArrayToPlainArray( $parser->setup );
	}


	/**
	 * Removes dots from config keys (copied from Extbase TypoScriptService class available since TYPO3 6.0)
	 *
	 * @param array $typoScriptArray TypoScript configuration array
	 * @return array Multi-dimensional, associative list of key/value pairs without dots in keys
	 */
	protected function _convertTypoScriptArrayToPlainArray(array $typoScriptArray)
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
