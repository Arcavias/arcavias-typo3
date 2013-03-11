<?php

########################################################################
# Extension Manager/Repository config file for ext: "arcavias"
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Arcavias',
	'description' => 'Flexible, high performance shop system',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.5.0',
	'dependencies' => 'extbase',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Metaways Software Engineering Team',
	'author_email' => 'typo3@metaways.de',
	'author_company' => 'Metaways Infosystems GmbH',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-5.99.99',
			'typo3' => '4.5.0-4.99.99',
			'extbase' => '1.3.2-4.99.99',
			'scheduler' => '1.1.0-4.99.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			'realurl' => '1.10.0-1.99.99',
			't3jquery' => '2.4.1-2.99.99',
		),
	),
);

?>