<?php

namespace PHPixie\Debug;

class Dumper
{
    public function shortDump($value)
    {
        return $this->dump($value, true);
    }
    
    public function dump($value, $short = false)
    {
        if(is_string($value)) {
            return $this->dumpString($value, $short);
        }
        
        if(!$short) {
            if($value instanceof Tracer\Trace) {
                return $this->dumpTrace($value);
            }
            
            if($value instanceof \Exception) {
                return $this->dumpException($value);
            }
        }
        
        if(is_object($value)) {
            return $this->dumpObject($value, $short);
        }
        
        if(is_array($value)) {
            return $this->dumpArray($value, $short);
        }
        
        return $this->dumpScalar($value);
    }
    
    protected function dumpString($value, $short)
    {
        if($short && strlen($value) > 15) {
            $value = substr($value, 0, 12).'...';
        }
        
        return "'$value'";
    }
    
    protected function dumpObject($object, $short)
    {
        if($short) {
            $class = get_class($object);
            $class = explode('\\', $class);
            return end($class);
        }
        
        return print_r($object, true);
    }
    
    protected function dumpArray($array, $short)
    {
        if($short) {
            $count = count($array);
            return "array[$count]";
        }
        
        return print_r($array, true);
    }
    
    protected function dumpTrace($trace)
    {
        $elementDumps = array();
        foreach($trace->elements() as $element) {
            $elementDumps[] = $this->dumpTraceElement($element);
        }
        
        return implode("\n", $elementDumps);
    }
    
    protected function dumpTraceElement($element)
    {
        $string = $element->context(true);
        if($context === null) {
            $string = $element->file();
        }
        
        $line = $element->line();
        if($line !== null) {
            $string.= ':'.$line;
        }
        
        return $string;
    }
    
    protected function dumpException($exception)
    {
        $tracer = $this->builder->tracer();
        $trace  = $tracer->exception($exception);
        
        $string = get_class($exception)." :\n";
        $string.= $exception->getMessage()."\n";
        $string.= $this->dumpTrace($trace);
        
        return $string;
    }
    
    protected function dumpScalar($value)
    {
        return var_export($value, true);
    }
}