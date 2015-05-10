<?php
require_once('vendor/autoload.php');

use PHPixie\Debug;
$debug = new Debug();

Debug::dump("Array dump:");
Debug::dump(array(1));

Debug::dump("Short array dump:");
Debug::dump(array(1), true);

$object = (object) array('t' => 1);
Debug::dump("Object dump:");
Debug::dump($object);

Debug::dump("Short object dump:");
Debug::dump($object, true);

echo "\n---Logging----\n";

Debug::log("test");
Debug::log(array(3));

class Test
{
    public function a($string, $num)
    {
        //Note how the trace
        //Will contain function parameters
        Debug::trace();
    }
}
$t = new Test();
$t->a("test", 5);

//The values will be printed
//only after this call
$debug->dumpLog();