<?php

// Register necessary classes with autoloader
$extensionPath = t3lib_extMgm::extPath( 'arcavias' );

return array(
	'Tx_Arcavias_Controller_Abstract' => $extensionPath . 'Classes/Controller/Abstract.php',

	'tx_arcavias_custom_realurl' => $extensionPath . 'Classes/Custom/Realurl.php',
	'tx_arcavias_custom_wizicon' => $extensionPath . 'Classes/Custom/Wizicon.php',

	'tx_arcavias_flexform_abstract' => $extensionPath . 'Classes/Flexform/Abstract.php',
	'tx_arcavias_flexform_catalog' => $extensionPath . 'Classes/Flexform/Catalog.php',

	'tx_arcavias_scheduler_abstract' => $extensionPath . 'Classes/Scheduler/Abstract.php',
	'tx_arcavias_scheduler_default' => $extensionPath . 'Classes/Scheduler/Default.php',
);

?>
