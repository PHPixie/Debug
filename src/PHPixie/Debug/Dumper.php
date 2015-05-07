<?php

namespace PHPixie\Debug;

class Dumper
{
    protected $showStringValues;
    
    public function __construct($configData)
    {
        $this->showStringValues = $configData->get('showStringValues', false);
    }
    
    public function dump($value)
    {
        if(is_string($value)) {
            return $this->dumpString($value);
        }
        
        if(is_object($value)) {
            return $this->dumpObject($value);
        }
        
        if(is_array($value)) {
            return $this->dumpArray($value);
        }
        
        return $this->dumpScalar($value);
    }
    
    protected function dumpString($value)
    {
        if($this->showStringValues) {
            return "'$value'";
        }
        
        $length = strlen($value);
        return "string[$length]";
    }
    
    protected function dumpObject($object)
    {
        $class = get_class($object);
        $class = explode('\\', $class);
        $class = end($class);
        
        return $class;
    }
    
    protected function dumpArray($array)
    {
        $count = count($array);
        return "array[$count]";
    }
    
    protected function dumpScalar($value)
    {
        return var_export($value, true);
    }
}