<?php

/**
* RealUrl Configuration for extension Arcavias
*
*/

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
			'table' => 'mshop_text mt INNER JOIN mshop_text_type mtt ON mtt.id = mt.typeid INNER JOIN mshop_catalog_list cl ON cl.refid=mt.id',
			'id_field' => 'parentid',
			'alias_field' => 'content',
			'addWhereClause' => ' AND cl.domain="text" AND (mt.langid="{TSFE:config|config|language}" or mt.langid is null) AND mt.status > 0 AND mt.domain="catalog" AND mtt.code="name" AND mtt.domain="catalog"',
			'useUniqueCache' => 1,
			'useUniqueCache_conf' => array(
				'strtolower' => 1,
				'spaceCharacter' => '-',
			),
			'insertData' => 1,
		),
	),
	array(
		'GETvar' => 'arc[l-product-id]',
		'lookUpTable' => array(
			'table' => 'mshop_text mt INNER JOIN mshop_text_type mtt ON mtt.id = mt.typeid INNER JOIN mshop_product_list pl ON pl.refid=mt.id',
			'id_field' => 'parentid',
			'alias_field' => 'content',
			'addWhereClause' => ' AND pl.domain="text" AND (mt.langid="{TSFE:config|config|language}" or mt.langid is null) AND mt.status > 0 AND mt.domain="product" AND mtt.code="name" AND mtt.domain="product"',
			'useUniqueCache' => 1,
			'useUniqueCache_conf' => array(
				'strtolower' => 1,
				'spaceCharacter' => '-',
			),
			'insertData' => 1,
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