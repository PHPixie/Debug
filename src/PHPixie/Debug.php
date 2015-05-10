<?php

namespace PHPixie;

class Debug
{
    protected $builder;
    
    static protected $instance;
    
    public function __construct()
    {
        $this->builder = $this->buildBuilder();
        static::$instance = $this;
    }
    
    public function builder()
    {
        return $this->builder;
    }
    
    public function logger()
    {
        return $this->builder->logger();
    }
    
    public function registerHandlers($shutdownLog = false, $exception = true, $error = true)
    {
        $this->builder->handlers()->register($shutdownLog, $exception, $error);
    }
    
    public function dumpLog($withTitle = true, $withTraceArguments = true, $shortValueDump = null, $echo = true)
    {
        $log = $this->builder->messages()->log($withTitle, $withTraceArguments, $shortValueDump);
        if($echo) {
            echo "\n$log\n";
        }
        
        return $log;
    }
    
    public function exceptionMessage($exception, $backtraceLimit = null, $neighboringLines = 5, $echo = true)
    {
        $message = $this->builder->messages()->exception($exception, $backtraceLimit, $neighboringLines);
        if($echo) {
            echo "\n$message\n";
        }
        
        return $message;
    }
    
    protected function buildBuilder()
    {
        return new Debug\Builder;
    }
    
    static public function log($value, $shortDump = false)
    {
        static::instanceBuilder()->logger()->log($value, $shortDump, 1);
    }
    
    static public function logTrace($limit = null, $offset = 0)
    {
        static::instanceBuilder()->logger()->trace($limit, 1+$offset);
    }
    
    static public function dump($value, $shortDump = false, $echo = true)
    {
        $dump = static::instanceBuilder()->dumper()->dump($value, $shortDump);
        if($echo) {
            echo "\n$dump\n";
        }
        
        return $dump;
    }
    
    static public function trace($limit = null, $offset = 0, $echo = true)
    {
        $trace = static::instanceBuilder()->tracer()->backtrace($limit, 1+$offset);
        if($echo) {
            echo "\n$trace\n";
        }
        
        return $trace;
    }
    
    static protected function instanceBuilder()
    {
        if(static::$instance === null) {
            throw new Debug\Exception("Debug library has not been initialized yet");
        }
        
        return static::$instance->builder;
    }

}