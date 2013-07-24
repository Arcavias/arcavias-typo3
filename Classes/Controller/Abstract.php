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
	static private $_i18n;
	static private $_config;
	static private $_context;
	static private $_arcavias;
	static private $_extConfig;


	/**
	 * Initializes the object before the real action is called.
	 */
	protected function initializeAction()
	{
		$this->uriBuilder->setArgumentPrefix( 'arc' );

		// Re-initialize the config object because the settings are different due to flexforms
		$this->_getContext()->setConfig( $this->_getConfig() );
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
		$config = $this->_getContext()->getConfig();
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Typo3( $view, $this->uriBuilder );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Translate_Default( $view, $this->_getI18n() );
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

			if( function_exists( 'apc_store' ) === true && $this->_getExtConfig( 'useAPC', false ) == true )
			{
				$prefix = ( isset( $this->settings['apc']['prefix'] ) ? $this->settings['apc']['prefix'] : '' );
				$conf = new MW_Config_Decorator_APC( $conf, $prefix );
			}

			self::$_config = $conf;
		}

		return new MW_Config_Decorator_Memory( self::$_config, (array) $this->settings );
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

			if( isset( $GLOBALS['TSFE']->fe_user ) )
			{
				$session = new MW_Session_Typo3( $GLOBALS['TSFE']->fe_user );
				$context->setSession( $session );
			}

			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			$context->setI18n( $this->_getI18n() );


			$langid = 'en';
			if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
				$langid = $GLOBALS['TSFE']->config['config']['language'];
			}

			$sitecode = $config->get( 'mshop/locale/site', 'default' );
			$currency = $config->get( 'mshop/locale/currency', 'EUR' );

			$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
			$locale = $localeManager->bootstrap( $sitecode, $langid, $currency );
			$context->setLocale( $locale );


			$username = $userid = null;

			if( TYPO3_MODE === 'BE' ) {
				$username = $GLOBALS['BE_USER']->user['username'];
			} elseif( TYPO3_MODE === 'FE' && $GLOBALS['TSFE']->loginUser == 1 ) {
				$username = $GLOBALS['TSFE']->fe_user->user['username'];
				$userid = $GLOBALS['TSFE']->fe_user->user[$GLOBALS['TSFE']->fe_user->userid_column];
			}

			$context->setEditor( $username );
			$context->setUserId( $userid );


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
				throw new Exception( 'Unable to get extension configuration' );
			}

			self::$_extConfig = $conf;
		}

		if( isset( self::$_extConfig[$name] ) ) {
			return self::$_extConfig[$name];
		}

		return $default;
	}


	/**
	 * Creates a new translation object.
	 *
	 * @return MW_Translation_Interface Configuration object
	 */
	protected function _getI18n()
	{
		if( !isset( self::$_i18n ) )
		{
			$langid = 'en';
			if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
				$langid = $GLOBALS['TSFE']->config['config']['language'];
			}

			$i18nPaths = $this->_getArcavias()->getI18nPaths();
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

				return new MW_Translation_Decorator_Memory( $i18n, $translations );
			}

			self::$_i18n = $i18n;
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
}
