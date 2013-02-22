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
	private $_includePaths;


	/**
	 * Initializes the controller.
	 */
	public function __construct()
	{
		parent::__construct();

		$includePaths = $this->_getMShop()->getIncludePaths();
		$includePaths[] = get_include_path();

		if( ( $this->_includePaths = set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) ) === false ) {
			throw new Exception( 'Unable to set include paths' );
		}
	}


	public function __destruct()
	{
		if( $this->_includePaths !== false ) {
			set_include_path( $this->_includePaths );
		}
	}


	/**
	 * Initializes the object before the real action is called.
	 */
	protected function initializeAction()
	{
		$this->uriBuilder->setArgumentPrefix( 'arc' );
		$context = $this->_getContext();


		// Re-initialize the config object because the settings are different due to flexforms
		$conf = $this->_createConfig( $this->settings );
		$context->setConfig( $conf );


		$langid = '';
		if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
			$langid = $GLOBALS['TSFE']->config['config']['language'];
		}

		$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
		$sitecode = $context->getConfig()->get( 'sitecode', 'default' );
		// @todo Get chosen currency from frontend
		$localeItem = $localeManager->bootstrap( $sitecode, $langid, '' );

		$context->setLocale( $localeItem );
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
		$ds = DIRECTORY_SEPARATOR;

		$configPaths = $this->_getMShop()->getConfigPaths( 'mysql' );
		$configPaths[] = t3lib_extMgm::extPath( 'arcavias' ) . 'Resources' . $ds . 'Private' . $ds . 'Config';

		$conf = new MW_Config_Array( $settings, $configPaths );
		if( function_exists( 'apc_store' ) === true && $this->_getExtConfig( 'useAPC', false ) == true ) {
			$conf = new MW_Config_Decorator_APC( $conf, $conf->get( 'typo3/apc/prefix' ) );
		}
		$conf = new MW_Config_Decorator_MemoryCache( $conf );

		return $conf;
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


			$i18nPaths = $this->_getMShop()->getI18nPaths();
			$i18n = new MW_Translation_Zend( $i18nPaths, 'gettext', 'en', array( 'disableNotices' => true ) );
			$context->setI18n( $i18n );


			if( isset( $GLOBALS['TSFE']->fe_user ) )
			{
				$session = new MW_Session_Typo3( $GLOBALS['TSFE']->fe_user );
				$context->setSession( $session );
			}


			$typo3User = null;

			if( TYPO3_MODE === 'BE' ) {
				$typo3User = $GLOBALS['BE_USER']->user['username'];
			} elseif( TYPO3_MODE === 'FE' && $GLOBALS['TSFE']->loginUser === 'login' ) {
				$typo3User = $GLOBALS['TSFE']->fe_user->user['username'];
			}

			$context->setEditor( $typo3User );


			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );


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

			if( spl_autoload_register( 'MShop::autoload' ) === false ) {
				throw new Exception( 'Unable to register Arcavias autoload method' );
			}

				// Hook for processing extension directories
			$extDirs = array();
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['arcavias']['extDirs']))
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
