<?php

/**
 * RealUrl Configuration for extension Arcavias
 */

// Use no caching for better performance of uncached pages
//$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['init'] = array(
//	'enableCHashCache' => true,
//	'enableUrlDecodeCache' => true,
//	'enableUrlEncodeCache' => true,
//);

// This creates a cHash mismatch so pages are not fetched from cache
//$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['fileName']['defaultToHTMLsuffixOnPrev'] = '';


$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['arcavias'] = array(
	array(
		'GETvar' => 'arc[controller]',
		'noMatch' => 'bypass',
	),
	array(
		'GETvar' => 'arc[action]',
		'noMatch' => 'bypass',
	),
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['fixedPostVars']['_DEFAULT'][] = array(
	'GETvar' => 'arc[f-catalog-id]',
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['fixedPostVars']['_DEFAULT'][] = array(
	'GETvar' => 'arc[l-product-id]',
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['c'][] = array(
	'GETvar' => 'arc[l-page]',
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['c'][] = array(
	'GETvar' => 'arc[l-sort]',
	'valueMap' => array(
		'name' => 'name',
		'price' => 'price',
	),
	'noMatch' => 'bypass',
);

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['c'][] = array(
	'GETvar' => 'arc[f-search-text]',
);


$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['b'][] = array( 'GETvar' => 'arc[b-action]' );
$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['b'][] = array( 'GETvar' => 'arc[b-position]' );
$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['b'][] = array( 'GETvar' => 'arc[b-quantity]' );


$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT']['postVarSets']['_DEFAULT']['o'][] = array(
	'GETvar' => 'arc[c-step]',
	'valueMap' => array(
		'address' => 'address',
		'delivery' => 'delivery',
		'payment' => 'payment',
		'summary' => 'summary',
		'order' => 'order',
	),
	'noMatch' => 'bypass',
);

?>