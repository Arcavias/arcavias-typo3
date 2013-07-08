<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 * @version $Id$
 */


/**
 * Arcavias update class for extension manager.
 *
 * @package TYPO3_Arcavias
 */
class ext_update
{
	/**
	 * Autoloader for setup tasks.
	 *
	 * @param string $classname Name of the class to load
	 * @return boolean True if class was found, false if not
	 */
	public static function autoload( $classname )
	{
		if( strncmp( $classname, 'MW_Setup_Task_', 14 ) === 0 )
		{
		    $fileName = substr( $classname, 14 ) . '.php';
			$paths = explode( PATH_SEPARATOR, get_include_path() );

			foreach( $paths as $path )
			{
				$file = $path . DIRECTORY_SEPARATOR . $fileName;

				if( file_exists( $file ) === true && ( include_once $file ) !== false ) {
					return true;
				}
			}
		}

		return false;
	}


	/**
	 * Returns the status if an update is necessary.
	 *
	 * @return boolean True if the update entry is available, false if not
	 */
	public function access()
	{
		return true;
	}


	/**
	 * Main update method called by the extension manager.
	 *
	 * @return string Messages
	 */
	public function main()
	{
		$ds = DIRECTORY_SEPARATOR;
		$basedir = dirname( __FILE__ );

		ini_set( 'max_execution_time', 0 );


		$exectimeStart = microtime( true );


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

		require $basedir . $ds . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

		$mshop = new MShop( $extDirs, false );


		$exectimeStart = microtime( true );

		$taskPaths = $mshop->getSetupPaths( 'default' );

		$includePaths = $taskPaths;
		$includePaths[] = get_include_path();

		if( set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) === false ) {
			throw new Exception( 'Unable to extend include path' );
		}

		if( spl_autoload_register( 'ext_update::autoload' ) === false ) {
			throw new Exception( 'Unable to register ext_update::autoload' );
		}

		$configPaths = $mshop->getConfigPaths( 'mysql' );
		$configPaths[] = $basedir . $ds  . 'Resources' . $ds  . 'Private' . $ds . 'Config';


		$ctx = $this->_getContext( $configPaths );

		$dbm = $ctx->getDatabaseManager();
		$conf = $ctx->getConfig();

		if( ( $dbconfig = $conf->get( 'resource/db' ) ) === null ) {
			throw new Exception( 'Configuration for database adapter missing' );
		}
		$conf->set( 'resource/db/limit', 2 );


		$manager = new MW_Setup_Manager_Default( $dbm->acquire(), $dbconfig, $taskPaths, $ctx );

		echo '<pre>';
		$manager->run( $dbconfig['adapter'] );
		echo '</pre>';


		return sprintf( "Setup process lasted %1\$f sec</br>\n", (microtime( true ) - $exectimeStart) );
	}


	/**
	 * Returns a new context object.
	 *
	 * @param array $configPaths List of configuration paths
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext( array $configPaths )
	{
		$ctx = new MShop_Context_Item_Default();

		$conf = new MW_Config_Array( array(), $configPaths );
		$ctx->setConfig( $conf );

		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );

		$logger = new MW_Logger_Errorlog( MW_Logger_ABSTRACT::INFO );
		$ctx->setLogger( $logger );

		$session = new MW_Session_None();
		$ctx->setSession( $session );

		return $ctx;
	}
}
