<?php

/**
* RealUrl Configuration for extension Arcavias
*
*/

/*
 * @todo: get language from TS?
 */
switch ( $_REQUEST['L'] )
{
	case 2:
		$lang = 'en';
		break;
	default:
		$lang = 'de';
}

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['arcavias'] = array(
	array(
		'GETvar' => 'arc[controller]'
	),
	array(
		'GETvar' => 'arc[action]'
	),
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['catalog'] = array(
	array(
		'GETvar' => 'arc[l-sort]',
		'valueMap' => array(
			'name' => 'name',
			'price' => 'price',
		),
		'noMatch' => 'bypass',
	),
	array(
		'GETvar' => 'arc[l-page]',
	),
	array(
		'GETvar' => 'arc[f-catalog-id]',
		'lookUpTable' => array(
			'table' => 'mshop_catalog_list cl, mshop_text mt, mshop_text_type mtt',
			'id_field' => 'parentid',
			'alias_field' => 'content',
			'addWhereClause' => ' AND mtt.code="name" AND mtt.domain="catalog" AND mtt.id=mt.typeid AND (mt.langid="'.$lang.'" or mt.langid is null) AND cl.refid=mt.id AND cl.domain="text" AND mt.status=1 AND mt.domain="catalog"',
			'useUniqueCache' => 1,
			'useUniqueCache_conf' => array(
				'strtolower' => 1,
				'spaceCharacter' => '-',
			),
		),
	),
	array(
		'GETvar' => 'arc[l-product-id]',
		'lookUpTable' => array(
			'table' => 'mshop_catalog_index_text ct',
			'id_field' => 'prodid',
			'alias_field' => 'value',
			'addWhereClause' => ' AND listtype=\'default\' AND type=\'name\' AND (ct.langid="'.$lang.'" or ct.langid is null)',
			'useUniqueCache' => 1,
			'useUniqueCache_conf' => array(
				'strtolower' => 1,
				'spaceCharacter' => '-',
			),
		),
	),
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['basket'] = array(
	array(
		'GETvar' => 'arc[b-action]'
	),
	array(
		'GETvar' => 'arc[b-position]'
	),
	array(
		'GETvar' => 'arc[b-quantity]'
	),
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['checkout'] = array(
	array(
		'GETvar' => 'arc[c-step]',
		'valueMap' => array(
			'delivery' => 'delivery',
			'payment' => 'payment',
			'summary' => 'summary',
			'order' => 'order',
		),
		'noMatch' => 'bypass',
	),
);

?>