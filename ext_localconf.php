<?php

if ( ! defined( 'TYPO3_MODE' ) ) {
	die ( 'Access denied.' );
}

/**
 * Include Arcavias extension directory
 */

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['extDirs'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Resources/Private/Libraries/ext/';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['confDirs'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Resources/Private/Config/';


/**
 * Arcavias plugins
 */

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-filter',
	array( 'Catalog' => 'filter' ),
	array()
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-filtersearch',
	array( 'Catalog' => 'filtersearch' ),
	array()
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-stage',
	array( 'Catalog' => 'stage' ),
	array()
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-list',
	array( 'Catalog' => 'list' ),
	array()
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
	array()
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


Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'account-history',
	array( 'Account' => 'history' ),
	array( 'Account' => 'history' )
);


/**
 * Arcavias scheduler tasks
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

$arcCfg = @unserialize( $_EXTCONF );
if( is_array( $arcCfg ) && isset( $arcCfg['useRealUrlAutoConfig'] ) && $arcCfg['useRealUrlAutoConfig'] != 0 ) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['arcavias'] =
		'EXT:arcavias/Classes/Custom/Realurl.php:tx_arcavias_custom_realurl->addAutoConfig';
}
unset( $arcCfg );

?>