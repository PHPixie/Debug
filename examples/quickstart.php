<?php
require_once('vendor/autoload.php');

use PHPixie\Debug;
$debug = new Debug();

Debug::dump(array(1));
Debug::dump(array(1), true);

$object = (object) array('t' => 1);
Debug::dump(array(1));
Debug::dump(array(1), true);