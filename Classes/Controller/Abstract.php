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
	private $_includePaths = false;


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


		$langid = '';
		if( isset( $GLOBALS['TSFE']->config['config']['language'] ) ) {
			$langid = $GLOBALS['TSFE']->config['config']['language'];
		}


		$context = $this->_getContext();

		$localeManager = MShop_Locale_Manager_Factory::createManager( $context );
		// @todo Get chosen currency from frontend
		$localeItem = $localeManager->bootstrap( $this->settings['sitecode'], $langid, '' );

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


	protected function _createView()
	{
		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Typo3( $view, $this->uriBuilder );
		$view->addHelper( 'url', $helper );

		$trans = new MW_Translation_Zend( $this->_getMShop()->getI18nPaths(), 'gettext', 'en', array( 'disableNotices' => true ) );
		$helper = new MW_View_Helper_Translate_Default( $view, $trans );
		$view->addHelper( 'translate', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, $this->request->getArguments() );
		$view->addHelper( 'param', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, $this->settings );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Number_Default( $view, $this->settings['format']['seperatorDecimal'], $this->settings['format']['seperator1000'] );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_Date_Default( $view, $this->settings['format']['date'] );
		$view->addHelper( 'date', $helper );

		/** @todo Parameter prefix should be set based on TypoScript configuration */
		$helper = new MW_View_Helper_FormParam_Default( $view, array( 'arc' ) );
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
			$ds = DIRECTORY_SEPARATOR;
			$context = new MShop_Context_Item_Default();


			$configPaths = $this->_getMShop()->getConfigPaths( 'mysql' );
			$configPaths[] = t3lib_extMgm::extPath( 'arcavias' ) . 'Resources' . $ds . 'Private' . $ds . 'Config';

			$conf = new MW_Config_Zend( new Zend_Config( array(), true ), $configPaths );

			if( function_exists( 'apc_store' ) === true && $this->_getExtConfig( 'useAPC', true ) == true ) {
				$conf = new MW_Config_Decorator_APC( $conf );
			}

			$context->setConfig( $conf );


			$dbm = new MW_DB_Manager_PDO( $conf );
			$context->setDatabaseManager( $dbm );


			$cache = new MW_Cache_None();
			$context->setCache( $cache );


			if( isset( $GLOBALS['TSFE']->fe_user ) )
			{
				$session = new MW_Session_Typo3( $GLOBALS['TSFE']->fe_user );
				$context->setSession( $session );
			}


			$typo3User = 'guest';

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

			self::$_mshop = new MShop( array( $libPath . $ds . 'ext' ), false, $libPath . $ds . 'core' );
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
