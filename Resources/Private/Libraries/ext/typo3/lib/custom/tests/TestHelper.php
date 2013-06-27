<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: TestHelper.php 16087 2012-07-17 11:11:41Z nsendetzky $
 */

class TestHelper
{
	private static $_mshop;
	private static $_context = array();


	public static function bootstrap()
	{
		$mshop = self::_getMShop();

		$includepaths = $mshop->getIncludePaths();
		$includepaths[] = get_include_path();
		set_include_path( implode( PATH_SEPARATOR, $includepaths ) );
	}


	public static function getContext( $site = 'unittest' )
	{
		if( !isset( self::$_context[$site] ) ) {
			self::$_context[$site] = self::_createContext( $site );
		}

		return self::$_context[$site];
	}


	private static function _getMShop()
	{
		if( !isset( self::$_mshop ) )
		{
			require_once 'MShop.php';
			spl_autoload_register( 'MShop::autoload' );

			$extdir = dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
			self::$_mshop = new MShop( array( $extdir ), false );
		}

		return self::$_mshop;
	}


	private static function _createContext( $site )
	{
		$ds = DIRECTORY_SEPARATOR;
		$ctx = new MShop_Context_Item_Default();
		$mshop = self::_getMShop();


		$paths = $mshop->getConfigPaths( 'mysql' );
		$paths[] = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_Array( array(), $paths );
		$ctx->setConfig( $conf );


		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );


		$logger = new MW_Logger_File( $site . '.log', MW_Logger_Abstract::DEBUG );
		$ctx->setLogger( $logger );


		$session = new MW_Session_None();
		$ctx->setSession( $session );


		$localeManager = MShop_Locale_Manager_Factory::createManager( $ctx );
		$localeItem = $localeManager->bootstrap( $site, '', '', false );

		$ctx->setLocale( $localeItem );

		$ctx->setEditor( 'test:typo3' );

		return $ctx;
	}
}
