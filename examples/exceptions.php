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
//all exception automatically
$debug->registerHandlers();

class Test
{
    public function a()
    {
        $this->b();
    }
    
    public function b()
    {
        $this->c();
    }
    
    public function c()
    {
        substr();
    }
}

$test = new Test();
$test->a();