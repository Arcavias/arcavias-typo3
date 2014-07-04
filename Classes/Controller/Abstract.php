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
abstract class Tx_Arcavias_Controller_Abstract
	extends Tx_Extbase_MVC_Controller_ActionController
{
	private $_arcavias;
	static private $_cache;
	static private $_locale;
	static private $_context;
	static private $_i18n = array();


	public function __construct()
	{
		parent::__construct();

		$this->_arcavias = Tx_Arcavias_Base::getArcavias();
	}


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


		if( !isset( self::$_cache ) )
		{
			/** @todo Use modern API in TYPO3 6.3 and above */
			// \TYPO3\CMS\Core\Cache\Cache::initializeCachingFramework();
			// $cache = GeneralUtility::makeInstance( 'TYPO3\\CMS\\Core\\Cache\\CacheManager' )->getCache( 'arcavias' );

			t3lib_cache::initializeCachingFramework();

			try
			{
				$cache = $GLOBALS['typo3CacheManager']->getCache( 'arcavias' );
			}
			catch( t3lib_cache_exception_NoSuchCache $e )
			{
				$cache = $GLOBALS['typo3CacheFactory']->create( 'arcavias',
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['arcavias']['frontend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['arcavias']['backend'],
					$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['arcavias']['options']
				);
			}

			self::$_cache = new MW_Cache_Typo3( array( 'siteid' => self::$_locale->getSiteId() ), $cache );
		}


		$context->setCache( self::$_cache );
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
	 * Creates a new configuration object.
	 *
	 * @return MW_Config_Interface Configuration object
	 */
	protected function _getConfig()
	{
		$settings = (array) $this->settings;

		if( isset( $this->settings['typo3']['tsconfig'] ) )
		{
			$tsconfig = Tx_Arcavias_Base::parseTS( $this->settings['typo3']['tsconfig'] );
			$settings = Tx_Extbase_Utility_Arrays::arrayMergeRecursiveOverrule( $settings, $tsconfig );
		}

		return Tx_Arcavias_Base::getConfig( $settings );
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
	 * Creates new translation objects.
	 *
	 * @param array $langIds List of two letter ISO language IDs
	 * @return array List of translation objects implementing MW_Translation_Interface
	 */
	protected function _getI18n( array $languageIds )
	{
		$i18nPaths = Tx_Arcavias_Base::getArcavias()->getI18nPaths();

		foreach( $languageIds as $langid )
		{
			if( !isset( self::$_i18n[$langid] ) )
			{
				$i18n = new MW_Translation_Zend( $i18nPaths, 'gettext', $langid, array( 'disableNotices' => true ) );

				if( function_exists( 'apc_store' ) === true && Tx_Arcavias_Base::getExtConfig( 'useAPC', false ) == true ) {
					$i18n = new MW_Translation_Decorator_APC( $i18n, Tx_Arcavias_Base::getExtConfig( 'apcPrefix', 't3:' ) );
				}

				if( isset( $this->settings['i18n'][$langid] ) )
				{
					$translations = array();

					foreach( (array) $this->settings['i18n'][$langid] as $entry )
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
}
