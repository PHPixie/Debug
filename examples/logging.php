<?php
require_once('vendor/autoload.php');

use PHPixie\Debug;
$debug = new Debug();

Debug::log("test");
Debug::log(array(3));

class Test
{
    public function a($string, $num)
    {
        //Note how the trace
        //Will contain function parameters
        Debug::logTrace();
    }
}
$t = new Test();
$t->a("test", 5);

//The values will be printed
//only after this call
$debug->dumpLog();