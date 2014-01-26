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
class Tx_Arcavias_Flexform_Abstract
{
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
			$arcavias = Tx_Arcavias_Base::getArcavias();
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
}
