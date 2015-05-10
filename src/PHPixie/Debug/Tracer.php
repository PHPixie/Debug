<?php

namespace PHPixie\Debug;

class Tracer
{
    protected $builder;
    
    public function __construct($builder)
    {
        $this->builder = $builder;
    }
    
    public function exceptionTrace($exception, $limit = null)
    {
        $first = $this->builder->traceElement(
            $exception->getFile(),
            $exception->getLine()
        );
        
        $trace = array_slice($exception->getTrace(), 0, $limit);
        $elements = $this->elementsFromTrace($trace);
        
        array_unshift($elements, $first);
        return $this->builder->trace($elements);
    }
    
    
    public function backtrace($limit = null, $offset = 0)
    {
        $trace = $this->debugBacktrace();
        $trace = array_slice($trace, $offset+1, $limit);
        
        $elements = $this->elementsFromTrace($trace);
        return $this->builder->trace($elements);
    }
    
    protected function elementsFromTrace($trace)
    {
        $elements = array();
        foreach($trace as $element) {
            
            $class = $this->get($element, 'class');
            if($class === 'PHPixie\Debug\Handlers') {
                continue;
            }
            
            $elements[]= $this->builder->traceElement(
                $this->get($element, 'file'),
                $this->get($element, 'line'),
                $this->get($element, 'function'),
                $this->get($element, 'args'),
                $class,
                $this->get($element, 'object'),
                $this->get($element, 'type')
            );
        }
        
        return $elements;
    }
    
    protected function debugBacktrace()
    {
        return debug_backtrace();
    }
    
    protected function get($array, $key)
    {
        if(array_key_exists($key, $array)) {
            return $array[$key];
        }
        
        return null;
    }
}