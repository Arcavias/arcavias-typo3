<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias abstract scheduler.
 *
 * @package TYPO3_Arcavias
 */
abstract class tx_arcavias_scheduler_abstract extends tx_scheduler_Task
{
	static private $_mshop;
	static private $_context;
	static private $_includePaths = false;
	private $_domainManagers = array();


	/**
	 * Creates the view object for the HTML client.
	 *
	 * @return MW_View_Interface View object
	 */
	protected function _createView()
	{
		$view = new MW_View_Default();

		$config = $this->_getContext()->getConfig();
		$config->set( 'client/html/email/confirm/main/html/encoded', false );
		$config->set( 'client/html/email/confirm/main/text/encoded', false );

		$helper = new MW_View_Helper_Config_Default( $view, $config );
		$view->addHelper( 'config', $helper );

		$sepDec = $config->get( 'client/html/common/format/seperatorDecimal', '.' );
		$sep1000 = $config->get( 'client/html/common/format/seperator1000', ' ' );
		$helper = new MW_View_Helper_Number_Default( $view, $sepDec, $sep1000 );
		$view->addHelper( 'number', $helper );

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

			// Important! Sets include paths
			$mshop = $this->_getMShop();
			$context = new MShop_Context_Item_Default();


			$configPaths = $mshop->getConfigPaths( 'mysql' );
			$configPaths[] = t3lib_extMgm::extPath( 'arcavias' ) . 'Resources' . $ds . 'Private' . $ds . 'Config';

			$conf = new MW_Config_Array( ( is_array( $this->settings ) ? $this->settings : array() ), $configPaths );
			$conf = new MW_Config_Decorator_MemoryCache( $conf );
			$context->setConfig( $conf );


			$dbm = new MW_DB_Manager_PDO( $conf );
			$context->setDatabaseManager( $dbm );


			$cache = new MW_Cache_None();
			$context->setCache( $cache );


			$context->setEditor( 'scheduler' );


			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );


			self::$_context = $context;
		}

		return self::$_context;
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


			$includePaths = self::$_mshop->getIncludePaths();
			$includePaths[] = get_include_path();

			if( ( self::$_includePaths = set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) ) === false ) {
				throw new Exception( 'Unable to set include paths' );
			}
		}

		return self::$_mshop;
	}


	/**
	 * Returns the manager for the given domain and sub-domains.
	 *
	 * @param string $domain String of domain and sub-domains, e.g. "product" or "order/base/service"
	 * @throws MShop_Exception If domain string is invalid or no manager can be instantiated
	 */
	protected function _getDomainManager( $domain )
	{
		$domain = strtolower( trim( $domain, "/ \n\t\r\0\x0B" ) );

		if( strlen( $domain ) === 0 ) {
			throw new MShop_Exception( 'An empty domain is invalid' );
		}

		if( !isset( $this->_domainManagers[$domain] ) )
		{
			$parts = explode( '/', $domain );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new MShop_Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
				}
			}

			if( ( $domainname = array_shift( $parts ) ) === null ) {
				throw new MShop_Exception( 'An empty domain is invalid' );
			}


			if( !isset( $this->_domainManagers[$domainname] ) )
			{
				$iface = 'MShop_Common_Manager_Interface';
				$factory = 'MShop_' . ucwords( $domainname ) . '_Manager_Factory';
				$manager = call_user_func_array( $factory . '::createManager', array( $this->_getContext() ) );

				if( !( $manager instanceof $iface ) ) {
					throw new MShop_Exception( sprintf( 'No factory "%1$s" found', $factory ) );
				}

				$this->_domainManagers[$domainname] = $manager;
			}


			foreach( $parts as $part )
			{
				$tmpname = $domainname .  '/' . $part;

				if( !isset( $this->_domainManagers[$tmpname] ) ) {
					$this->_domainManagers[$tmpname] = $this->_domainManagers[$domainname]->getSubManager( $part );
				}

				$domainname = $tmpname;
			}
		}

		return $this->_domainManagers[$domain];
	}


	/**
	 * Starts a new transaction for the current connection.
	 */
	protected function _beginTx()
	{
		$dbm = $this->_getContext()->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->begin();
		$dbm->release( $conn );
	}


	/**
	 * Commits an existing transaction for the current connection.
	 */
	protected function _commitTx()
	{
		$dbm = $this->_getContext()->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->commit();
		$dbm->release( $conn );
	}


	/**
	 * Rolls back an existing transaction for the current connection.
	 */
	protected function _rollbackTx()
	{
		$dbm = $this->_getContext()->getDatabaseManager();

		$conn = $dbm->acquire();
		$conn->rollback();
		$dbm->release( $conn );
	}
}
