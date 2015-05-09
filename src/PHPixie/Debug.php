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
    
    public function dumper()
    {
        return $this->builder->dumper();
    }
    
    public function registerHandlers()
    {
        $this->builder->errorHandler()->register();
    }
    
    public function dumpLog($withTitle = true, $withTraceArguments = true, $shortValueDump = null, $echo = true)
    {
        $log = $this->builder->messages()->log($withTitle, $withTraceArguments, $shortValueDump);
        if($echo) {
            echo $log."\n";
        }
        
        return $log;
    }
    
    public function exceptionMessage($exception, $backtraceLimit = null, $neighboringLines = 5, $echo = true)
    {
        $message = $this->builder->messages()->exception($exception, $backtraceLimit, $neighboringLines);
        if($echo) {
            echo $message."\n";
        }
        
        return $message;
    }
    
    protected function buildBuilder()
    {
        return new Debug\Builder;
    }
    
    static public function log($value, $shortDump = false)
    {
        static::instance()->logger()->log($value, $shortDump);
    }
    
    static public function trace($limit)
    {
        static::instance()->logger()->trace($limit);
    }
    
    static public function dump($value, $shortDump = false, $echo = true)
    {
        $dump = static::instance()->dumper()->dump($value, $shortDump);
        if($echo) {
            echo $dump."\n";
        }
        
        return $dump;
    }
    
    static protected function instance()
    {
        if(static::$instance === null) {
            throw new Debug\Exception("Debug library has not been initialized yet");
        }
        
        return static::$instance;
    }

}