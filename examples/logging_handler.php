<?php
require_once('vendor/autoload.php');

use PHPixie\Debug;
$debug = new Debug();

//Passing 'true' here will enable printing logs
//automatically after the end of script execution.
//This is very useful if your code relies
//on exit() calls
$debug->registerHandlers(true);

Debug::log("test");

echo("Logged messages will be printed now");