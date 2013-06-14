<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Abstract class with common functionality for all controllers.
 *
 * @package TYPO3_Arcavias
 */
abstract class Tx_Arcavias_Controller_Abstract extends Tx_Extbase_MVC_Controller_ActionController
{
	static private $_mshop;
	static private $_context;
	static private $_extConfig;
	static private $_includePaths;
	static private $_configPaths;


	/**
	 * Initializes the controller.
	 */
	public function __construct()
	{
		parent::__construct();

		if( self::$_includePaths === null )
		{
			$ds = DIRECTORY_SEPARATOR;

			$includePaths = $this->_getMShop()->getIncludePaths();
			$includePaths[] = t3lib_extMgm::extPath( 'arcavias' ) . 'Resources' . $ds . 'Private' . $ds . 'Libraries' . $ds . 'zendlib';
			$includePaths[] = get_include_path();

			if( ( self::$_includePaths = set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) ) === false ) {
				throw new Exception( 'Unable to set include paths' );
			}
		}

		if( self::$_configPaths === null )
		{
			$configPaths = $this->_getMShop()->getConfigPaths( 'mysql' );

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

			self::$_configPaths = $configPaths;
		}
	}


	/**
	 * Initializes the object before the real action is called.
	 */
	protected function initializeAction()
	{
		$this->uriBuilder->setArgumentPrefix( 'arc' );

		// Re-initialize the config object because the settings are different due to flexforms
		$conf = $this->_createConfig( $this->settings );
		$this->_getContext()->setConfig( $conf );
	}


	/**
	 * Creates a special Arcavias view for performance reasons.
	 *
	 * return Tx_Extbase_MVC_View_ViewInterface View object
	 */
	protected function resolveView()
	{
		return null;
	}


	/**
	 * Creates a new configuration object.
	 *
	 * @param array $settings Multi-dimensional list of initial configuration settings
	 * @return MW_Config_Interface Configuration object
	 */
	protected function _createConfig( array $settings )
	{
		$conf = new MW_Config_Array( array(), self::$_configPaths );

		if( function_exists( 'apc_store' ) === true && $this->_getExtConfig( 'useAPC', false ) == true )
		{
			$prefix = ( isset( $settings['apc']['prefix'] ) ? $settings['apc']['prefix'] : '' );
			$conf = new MW_Config_Decorator_APC( $conf, $prefix );
		}

		return new MW_Config_Decorator_MemoryCache( $conf, $settings );
	}


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @return MW_View_Interface View object
	 */
	protected function _createView()
	{
		$langid = 'en';
		if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
			$langid = $GLOBALS['TSFE']->config['config']['language'];
		}

		$config = $this->_getContext()->getConfig();
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Typo3( $view, $this->uriBuilder );
		$view->addHelper( 'url', $helper );

		$trans = new MW_Translation_Zend( $this->_getMShop()->getI18nPaths(), 'gettext', $langid, array( 'disableNotices' => true ) );
		if( function_exists( 'apc_store' ) === true && $this->_getExtConfig( 'useAPC', false ) == true ) {
			$trans = new MW_Translation_Decorator_APC( $trans, $config );
		}
		$helper = new MW_View_Helper_Translate_Default( $view, $trans );
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

		return $view;
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


			$conf = $this->_createConfig( ( is_array( $this->settings ) ? $this->settings : array() ) );
			$context->setConfig( $conf );


			$dbm = new MW_DB_Manager_PDO( $conf );
			$context->setDatabaseManager( $dbm );


			$cache = new MW_Cache_None();
			$context->setCache( $cache );


			$langid = 'en';
			if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
				$langid = $GLOBALS['TSFE']->config['config']['language'];
			}

			$i18nPaths = $this->_getMShop()->getI18nPaths();
			$i18n = new MW_Translation_Zend( $i18nPaths, 'gettext', $langid, array( 'disableNotices' => true ) );
			$context->setI18n( $i18n );


			if( isset( $GLOBALS['TSFE']->fe_user ) )
			{
				$session = new MW_Session_Typo3( $GLOBALS['TSFE']->fe_user );
				$context->setSession( $session );
			}


			$username = $userid = null;

			if( TYPO3_MODE === 'BE' ) {
				$username = $GLOBALS['BE_USER']->user['username'];
			} elseif( TYPO3_MODE === 'FE' && $GLOBALS['TSFE']->loginUser == 1 ) {
				$username = $GLOBALS['TSFE']->fe_user->user['username'];
				$userid = $GLOBALS['TSFE']->fe_user->user[$GLOBALS['TSFE']->fe_user->userid_column];
			}

			$context->setEditor( $username );
			$context->setUserId( $userid );


			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );


			$sitecode = $context->getConfig()->get( 'mshop/locale/site', 'default' );
			$currency = $context->getConfig()->get( 'mshop/locale/currency', 'EUR' );

			$langid = '';
			if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
				$langid = $GLOBALS['TSFE']->config['config']['language'];
			}

			$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
			$locale = $localeManager->bootstrap( $sitecode, $langid, $currency );
			$context->setLocale( $locale );


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
	 * Returns the MShop object.
	 *
	 * @return MShop MShop object
	 */
	protected function _getMShop()
	{
		if( self::$_mshop === null )
		{
			$ds = DIRECTORY_SEPARATOR;
			$libPath = t3lib_extMgm::extPath( 'arcavias' ) . 'Resources' . $ds . 'Private' . $ds . 'Libraries';

			require_once $libPath . $ds . 'core' . $ds . 'MShop.php';

			if( spl_autoload_register( 'MShop::autoload', true, true ) === false ) {
				throw new Exception( 'Unable to register Arcavias autoload method' );
			}

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

			self::$_mshop = new MShop( $extDirs, false, $libPath . $ds . 'core' );
		}

		return self::$_mshop;
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
