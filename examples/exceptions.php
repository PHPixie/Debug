<?php
require_once('vendor/autoload.php');
$debug = new \PHPixie\Debug();
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

try{
    $test->a();
    
}catch(\Exception $e) {
    $debug->exceptionMessage($e);
}