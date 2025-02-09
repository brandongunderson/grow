<?php
// config.php

// Define the BASE_URL constant for your project.
// If your project is in a folder (e.g., http://localhost/growapp/), include the folder in the path.
// If it's in the web server's root, you can set it to '/'.
define('BASE_URL', '/growapp/');

// You can also add database configuration settings if needed:
define('DB_HOST', 'localhost');
define('DB_NAME', 'grow');
define('DB_USER', 'zaragenetics');
define('DB_PASS', 'Vxupwyyrt1!');

// Optionally, set the default timezone
date_default_timezone_set('America/New_York');

// You might also configure error reporting here for development:
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
