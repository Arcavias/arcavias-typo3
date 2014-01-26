<?php

// Register necessary classes with autoloader
$extensionPath = t3lib_extMgm::extPath( 'arcavias' );

return array(
	'tx_arcavias_base' => $extensionPath . 'Classes/Base.php',

	'tx_arcavias_controller_abstract' => $extensionPath . 'Classes/Controller/Abstract.php',
	'tx_arcavias_controller_accountcontroller' => $extensionPath . 'Classes/Controller/AccountController.php',
	'tx_arcavias_controller_admincontroller' => $extensionPath . 'Classes/Controller/AdminController.php',
	'tx_arcavias_controller_basketcontroller' => $extensionPath . 'Classes/Controller/BasketController.php',
	'tx_arcavias_controller_catalogcontroller' => $extensionPath . 'Classes/Controller/CatalogController.php',
	'tx_arcavias_controller_checkoutcontroller' => $extensionPath . 'Classes/Controller/CheckoutController.php',

	'tx_arcavias_custom_realurl' => $extensionPath . 'Classes/Custom/Realurl.php',
	'tx_arcavias_custom_wizicon' => $extensionPath . 'Classes/Custom/Wizicon.php',

	'tx_arcavias_flexform_abstract' => $extensionPath . 'Classes/Flexform/Abstract.php',
	'tx_arcavias_flexform_catalog' => $extensionPath . 'Classes/Flexform/Catalog.php',

	'tx_arcavias_scheduler_base' => $extensionPath . 'Classes/Scheduler/Base.php',
	'tx_arcavias_scheduler_task_typo4' => $extensionPath . 'Classes/Scheduler/Task/Typo4.php',
	'tx_arcavias_scheduler_provider_typo4' => $extensionPath . 'Classes/Scheduler/Provider/Typo4.php',
	'Arcavias\Arcavias\Scheduler\Task\Typo6' => $extensionPath . 'Classes/Scheduler/Task/Typo6.php',
	'Arcavias\Arcavias\Scheduler\Provider\Typo6' => $extensionPath . 'Classes/Scheduler/Provider/Typo6.php',
);

?>
