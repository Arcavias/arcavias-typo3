<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


/**
 * Arcavias abstract flexform helper.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_flexform_abstract
{
	private $_mshop;
	private $_context;


	/**
	 * Returns the current context.
	 *
	 * @return MShop_Context_Item_Interface Context object
	 */
	protected function _getContext()
	{
		if( $this->_context === null )
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


			$context->setEditor( 'flexform' );


			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );


			$this->_context = $context;
		}

		return $this->_context;
	}


	/**
	 * Returns the MShop object.
	 *
	 * @return MShop MShop object
	 */
	protected function _getMShop()
	{
		if( $this->_mshop === null )
		{
			$ds = DIRECTORY_SEPARATOR;
			$libPath = t3lib_extMgm::extPath( 'arcavias' ) . 'Resources' . $ds . 'Private' . $ds . 'Libraries';

			require_once $libPath . $ds . 'core' . $ds . 'MShop.php';

			if( spl_autoload_register( 'MShop::autoload' ) === false ) {
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

			$this->_mshop = new MShop( $extDirs, false, $libPath . $ds . 'core' );


			$includePaths = $this->_mshop->getIncludePaths();
			$includePaths[] = get_include_path();

			if( ( $this->_includePaths = set_include_path( implode( PATH_SEPARATOR, $includePaths ) ) ) === false ) {
				throw new Exception( 'Unable to set include paths' );
			}
		}

		return $this->_mshop;
	}
}
