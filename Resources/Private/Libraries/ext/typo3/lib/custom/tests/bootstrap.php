<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @version $Id: bootstrap.php 14383 2011-12-16 13:37:23Z gwussow $
 */

/*
 * Set error reporting to maximum
 */
error_reporting( -1 );
ini_set('display_errors', true);


/*
 * Set locale settings to reasonable defaults
 */
setlocale(LC_ALL, 'en_US.UTF-8');
setlocale(LC_NUMERIC, 'POSIX');
setlocale(LC_CTYPE, 'en_US.UTF-8');
setlocale(LC_TIME, 'POSIX');
date_default_timezone_set( 'UTC' );

require_once 'TestHelper.php';
TestHelper::bootstrap();
