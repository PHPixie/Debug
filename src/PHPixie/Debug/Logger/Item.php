<?php

namespace PHPixie\Debug\Logger;

class Item
{
    protected $dumper;
    protected $traceElement;
    protected $value;
    
    protected $shortDumpByDefault;
    
    public function __construct($dumper, $traceElement, $value, $shortDumpByDefault = false)
    {
        $this->dumper             = $dumper;
        $this->traceElement       = $traceElement;
        $this->value              = $value;
        $this->shortDumpByDefault = $shortDumpByDefault;
    }
    
    public function traceElement()
    {
        return $this->traceElement;
    }
    
    public function value()
    {
        return $this->value;
    }
    
    public function valueDump($short = null)
    {
        if($short === null) {
            $short = $this->shortDumpByDefault;
        }
        
        return $this->dumper->dump($this->value, $short);
    }
    
    public function asString($withTraceArguments = true, $shortValueDump = null)
    {
        $string = $this->traceElement->asString($withTraceArguments, false);
        $string.= "\n".$this->valueDump($shortValueDump);
        return $string;
    }
    
    public function __toString()
    {
        return $this->asString();
    }
}