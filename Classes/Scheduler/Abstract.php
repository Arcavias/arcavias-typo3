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

			self::$_mshop = new MShop( array( $libPath . $ds . 'ext' ), false, $libPath . $ds . 'core' );


			$includePaths = self::$_mshop->getIncludePaths();
			$includePaths[] = get_include_path();

			if( ( self::$_includePaths = set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) ) === false ) {
				throw new Exception( 'Unable to set include paths' );
			}
		}

		return self::$_mshop;
	}
}
