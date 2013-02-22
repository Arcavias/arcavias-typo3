<?php

if ( ! defined( 'TYPO3_MODE' ) ) {
	die ( 'Access denied.' );
}

$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl/class.tx_realurl.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'class.ux_tx_realurl.php';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['extDirs'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Resources/Private/Libraries/ext/';

/*
 * Plugins
 */

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-filter',
	array( 'Catalog' => 'filter' ),
	array( 'Catalog' => 'filter' )
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-filtersearch',
	array( 'Catalog' => 'filtersearch' ),
	array()
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-list',
	array( 'Catalog' => 'list' ),
	array( 'Catalog' => 'list' )
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-listsimple',
	array( 'Catalog' => 'listsimple' ),
	array( 'Catalog' => 'listsimple' )
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-detail',
	array( 'Catalog' => 'detail' ),
	array( 'Catalog' => 'detail' )
);


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'basket-small',
	array( 'Basket' => 'small' ),
	array( 'Basket' => 'small' )
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'basket-standard',
	array( 'Basket' => 'index' ),
	array( 'Basket' => 'index' )
);


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'checkout-standard',
	array( 'Checkout' => 'index' ),
	array( 'Checkout' => 'index' )
);


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'checkout-confirm',
	array( 'Checkout' => 'confirm' ),
	array( 'Checkout' => 'confirm' )
);


/*
 * Scheduler tasks
 */
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_arcavias_scheduler_catalog'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:catalog.name',
	'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:catalog.description',
	'additionalFields' => 'tx_arcavias_scheduler_catalog',
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['tx_arcavias_scheduler_maintenance'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:maintenance.name',
	'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:maintenance.description',
	'additionalFields' => 'tx_arcavias_scheduler_maintenance',
);


/**
* Add RealURL Configuration
*/
$config = @unserialize( $_EXTCONF );
if ( is_array( $config ) )
{
	if( $config['useRealUrlAutoConfig'] )
	{
		$filepath = 'EXT:' . $_EXTKEY . '/realurl_autoconf.php';
		$filepath = t3lib_div::getFileAbsFileName( $filepath );
		require_once( $filepath );
	}
}
unset( $config );

?>