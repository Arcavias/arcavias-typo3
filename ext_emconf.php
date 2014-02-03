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
	'version' => '0.10.0',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'author' => 'Metaways Software Engineering Team',
	'author_email' => 'typo3@metaways.de',
	'author_company' => 'Metaways Infosystems GmbH',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-5.99.99',
			'typo3' => '4.5.0-6.2.99',
			'extbase' => '1.3.2-6.99.99',
			'scheduler' => '1.1.0-6.99.99',
			'static_info_tables' => '1.8.0-6.99.99',
		),
		'conflicts' => array(
			'jquerycolorbox' => '0.0.0-0.0.1',
		),
		'suggests' => array(
			'realurl' => '1.10.0-1.99.99',
			'datamints_feuser' => '0.6.4-1.99.99',
			'sr_feuser_register' => '2.6.3-3.99.99',
		),
	),
);

?>
