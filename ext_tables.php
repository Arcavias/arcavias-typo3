<?php

if ( ! defined( 'TYPO3_MODE' ) ) {
	die ( 'Access denied.' );
}


t3lib_extMgm::addStaticFile( $_EXTKEY, 'Configuration/TypoScript/', 'Arcavias configuration' );


if ( TYPO3_MODE === 'BE' )
{
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'user',
		'tx_arcavias_admin',
		'', // position
		array(
			'Admin' => 'index,do',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/Admin.xml',
		)
	);
}


$pluginName = str_replace( '_', '', $_EXTKEY );

$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginName . '_catalog-filter'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue( $pluginName . '_catalog-filter', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/CatalogFilter.xml' );
Tx_Extbase_Utility_Extension::registerPlugin( $_EXTKEY, 'catalog-filter', 'Arcavias - Catalog filter' );

$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginName . '_catalog-list'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue( $pluginName . '_catalog-list', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/CatalogList.xml' );
Tx_Extbase_Utility_Extension::registerPlugin( $_EXTKEY, 'catalog-list', 'Arcavias - Catalog list' );

$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginName . '_catalog-detail'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue( $pluginName . '_catalog-detail', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/CatalogDetail.xml' );
Tx_Extbase_Utility_Extension::registerPlugin( $_EXTKEY, 'catalog-detail', 'Arcavias - Catalog detail' );


$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginName . '_basket-standard'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue( $pluginName . '_basket-standard', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/BasketStandard.xml' );
Tx_Extbase_Utility_Extension::registerPlugin( $_EXTKEY, 'basket-standard', 'Arcavias - Basket standard' );

?>
