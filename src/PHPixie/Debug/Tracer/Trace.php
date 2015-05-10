<?php

namespace PHPixie\Debug\Tracer;

class Trace
{
    protected $elements;
    
    public function __construct($elements = array())
    {
        $this->elements = $elements;
    }
    
    public function elements()
    {
        return $this->elements;
    }
    
    public function asString($withArguments = true)
    {
        $string = '';
        foreach($this->elements as $key => $element) {
            if($key > 0) {
                $string.="\n";
            }
            
            $string.= '#'.$key.' '.$element->asString($withArguments);
        }
        
        return $string;
    }
    
    public function __toString()
    {
        return $this->asString();
    }
}