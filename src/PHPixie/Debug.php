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
            static::output($log);
        }
        
        return $log;
    }
    
    public function exceptionMessage($exception, $backtraceLimit = null, $neighboringLines = 5, $echo = true)
    {
        $message = $this->builder->messages()->exception($exception, $backtraceLimit, $neighboringLines);
        if($echo) {
            static::output($message);
        }
        
        return $message;
    }
    
    public function exceptionTrace($exception, $backtraceLimit = null)
    {
        return $this->builder->tracer()->exceptionTrace($exception, $backtraceLimit);
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
            static::output($dump);
        }
        
        return $dump;
    }
    
    static public function trace($limit = null, $offset = 0, $echo = true)
    {
        $trace = static::instanceBuilder()->tracer()->backtrace($limit, 1+$offset);
        if($echo) {
            static::output($trace);
        }
        
        return $trace;
    }
    
    static protected function output($string)
    {
        echo "\n$string\n";
    }
    
    static protected function instanceBuilder()
    {
        if(static::$instance === null) {
            throw new Debug\Exception("Debug library has not been initialized yet");
        }
        
        return static::$instance->builder;
    }

}