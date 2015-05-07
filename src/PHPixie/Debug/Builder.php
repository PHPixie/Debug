<?php

namespace PHPixie\Debug;

class Builder
{
    protected $instances = array();
    
    public function dumper()
    {
        return $this->instance('dumper');
    }
    
    public function logger()
    {
        return $this->instance('logger');
    }
    
    public function tracer()
    {
        return $this->instance('tracer');
    }
    
    public function loggerItem($traceElement, $value, $shortDumpByDefault = false)
    {
        return new Logger\Item(
            $this->dumper(),
            $traceElement,
            $value,
            $shortDumpByDefault
        );
    }
    
    public function trace($elements = array())
    {
        return new Tracer\Trace($elements);
    }
    
    public function traceElement(
        $file         = null,
        $line         = null,
        $functionName = null,
        $arguments    = null,
        $className    = null,
        $object       = null,
        $type         = null
    )
    {
        return new Tracer\Trace\Element(
            $this->dumper(),
            $file,
            $line,
            $functionName,
            $arguments,
            $className,
            $object,
            $type
        );
    }
    
    protected function instance($name)
    {
        if(!array_key_exists($name, $this->instances)) {
            $method = 'build'.ucfirst($name);
            $this->instances[$name] = $this->$method();
        }
        
        return $this->instances[$name];
    }
    
    protected function buildDumper()
    {
        return new Dumper();
    }
    
    protected function buildLogger()
    {
        return new Logger($this);
    }
    
    protected function buildTracer()
    {
        return new Tracer($this);
    }
}