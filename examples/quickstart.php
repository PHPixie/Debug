<?php
require_once('vendor/autoload.php');

use PHPixie\Debug;
$debug = new Debug();

Debug::dump(array(1));
Debug::dump(array(1), true);

$object = (object) array('t' => 1);
Debug::dump($object);
Debug::dump($object, true);

//Logging
Debug::log("test");
Debug::trace(1);
Debug::log(array(3));

$debug->dumpLog();