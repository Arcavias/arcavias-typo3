<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license GPLv3, http://www.gnu.org/copyleft/gpl.html
 * @package TYPO3_Arcavias
 */


require_once dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


/**
 * Arcavias abstract flexform helper.
 *
 * @package TYPO3_Arcavias
 */
class tx_arcavias_flexform_abstract
{
	private $_arcavias;
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
			$arcavias = $this->_getArcavias();
			$context = new MShop_Context_Item_Default();


			$configPaths = $arcavias->getConfigPaths( 'mysql' );
			$configPaths[] = t3lib_extMgm::extPath( 'arcavias' ) . 'Resources' . $ds . 'Private' . $ds . 'Config';

			$conf = new MW_Config_Array( array(), $configPaths );
			$conf = new MW_Config_Decorator_Memory( $conf );
			$context->setConfig( $conf );

			$dbm = new MW_DB_Manager_PDO( $conf );
			$context->setDatabaseManager( $dbm );

			$logger = MAdmin_Log_Manager_Factory::createManager( $context );
			$context->setLogger( $logger );

			$context->setEditor( 'flexform' );


			$this->_context = $context;
		}

		return $this->_context;
	}


	/**
	 * Returns the Arcavias object.
	 *
	 * @return Arcavias Arcavias object
	 */
	protected function _getArcavias()
	{
		if( $this->_arcavias === null )
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

			$this->_arcavias = new Arcavias( $extDirs, false, $libPath );
		}

		return $this->_arcavias;
	}
}
