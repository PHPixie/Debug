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
}