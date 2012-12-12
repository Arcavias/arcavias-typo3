<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: manifest.php 16087 2012-07-17 11:11:41Z nsendetzky $
 */

return array(
	'name' => 'Arcavias TYPO3',
	'description' => 'TYPO3 extension for Arcavias shop system',
	'author' => 'Metaways Infosystems GmbH',
	'email' => 'eshop@metaways.de',
	'version' => '2011-08',
	'depends' => array(
		array( '>=' => array( 'Arcavias' => '2011-03' ) ),
	),
	'conflicts' => array(
	),
	'include' => array(
		'lib/custom/src',
	),
	'config' => array(
		'mysql' => array(
			'lib/custom/config/common',
			'lib/custom/config/mysql',
		),
	),
	'setup' => array(
		'lib/custom/setup',
	),
);
