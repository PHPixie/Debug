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

Debug::dump("Dump trace with parameters");
class Test
{
    public function a($string)
    {
        $array = array(1, 2);
        $this->b($string, $array);
    }
    
    public function b($string, $array)
    {
        $object = (object) array('t' => 1);
        $this->c($string, $array, $object);
    }
    
    public function c()
    {
        Debug::trace();
    }
}

$t = new Test();
$t->a("test");