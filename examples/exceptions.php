<?php
require_once('vendor/autoload.php');
$debug = new \PHPixie\Debug();

//Pretty printing exceptions
try{
    throw new \Exception("test");
    
}catch(\Exception $e) {
    $debug->exceptionMessage($e);
}


echo "\n-------\n";


//Register handlers to pretty print
//all exception automatically.
//Logged items will also be printed
$debug->registerHandlers();

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
        substr();
    }
}

$test = new Test();
$test->a("pixie");