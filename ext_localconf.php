<?php

if ( ! defined( 'TYPO3_MODE' ) ) {
	die ( 'Access denied.' );
}


require_once t3lib_extMgm::extPath( 'arcavias' ) . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


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
	array( 'Catalog' => 'filter' )
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-stage',
	array( 'Catalog' => 'stage' ),
	array( 'Catalog' => 'stage' )
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-count',
	array( 'Catalog' => 'count' ),
	array( 'Catalog' => 'count' )
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-stock',
	array( 'Catalog' => 'stock' ),
	array( 'Catalog' => 'stock' )
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
	array()
);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'catalog-session',
	array( 'Catalog' => 'session' ),
	array( 'Catalog' => 'session' )
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
	'checkout-update',
	array( 'Checkout' => 'update' ),
	array( 'Checkout' => 'update' )
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

if( class_exists( '\TYPO3\CMS\Scheduler\Task\AbstractTask' ) )
{
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Arcavias\Arcavias\Scheduler\Task\Typo6'] = array(
		'extension'        => $_EXTKEY,
		'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:default.name',
		'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:default.description',
		'additionalFields' => 'Arcavias\Arcavias\Scheduler\Provider\Typo6',
	);
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Arcavias\Arcavias\Scheduler\Task\Email6'] = array(
		'extension'        => $_EXTKEY,
		'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:email.name',
		'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:email.description',
		'additionalFields' => 'Arcavias\Arcavias\Scheduler\Provider\Email6',
	);
}
else
{
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_Arcavias_Scheduler_Task_Typo4'] = array(
		'extension'        => $_EXTKEY,
		'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:default.name',
		'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:default.description',
		'additionalFields' => 'Tx_Arcavias_Scheduler_Provider_Typo4',
	);
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Tx_Arcavias_Scheduler_Task_Email4'] = array(
		'extension'        => $_EXTKEY,
		'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:email.name',
		'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Scheduler.xml:email.description',
		'additionalFields' => 'Tx_Arcavias_Scheduler_Provider_Email4',
	);
}


/**
 * Add RealURL Configuration
 */

if( Tx_Arcavias_Base::getExtConfig( 'useRealUrlAutoConfig', 1 ) != 0 ) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['arcavias'] =
		'EXT:arcavias/Classes/Custom/Realurl.php:tx_arcavias_custom_realurl->addAutoConfig';
}

?>