<?php

/*
 * Common resource configuration file
 */

return array(

	// standard database configuration
	'db' => array(
	
		// database adapter (currently only 'mysql')
		'adapter' => 'mysql',
		
		// database server or path to local (socket) file
		'host' => 'localhost',
		
		// name of the database to use
		'database' => 'mshop',
		
		// name of the database account used for connecting 
		'username' => 'mshop',
		
		// secret password
		'password' => 'mshop',
		
		// SQL statements executed immediately after connecting to the database server
		'stmt' => array( "SET NAMES 'utf8'", "SET SESSION sql_mode='ANSI'" ),
		
		// number of concurrent database connections while processing one request
		'limit' => 2, 
	),
);
