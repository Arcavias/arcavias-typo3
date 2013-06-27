<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: manifest.php 16087 2012-07-17 11:11:41Z nsendetzky $
 */

return array(
	'name' => 'typo3',
	'depends' => array(
		'arcavias-core',
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
