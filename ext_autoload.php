<?php

// Register necessary classes with autoloader
$extensionPath = t3lib_extMgm::extPath( 'arcavias' );

return array(
	'tx_arcavias_scheduler_abstract' => $extensionPath . 'Classes/Scheduler/Abstract.php',
	'tx_arcavias_scheduler_admin' => $extensionPath . 'Classes/Scheduler/Admin.php',
	'tx_arcavias_scheduler_catalog' => $extensionPath . 'Classes/Scheduler/Catalog.php',

	'tx_arcavias_flexform_abstract' => $extensionPath . 'Classes/Flexform/Abstract.php',
	'tx_arcavias_flexform_catalog' => $extensionPath . 'Classes/Flexform/Catalog.php',
);

?>
