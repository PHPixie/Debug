<?php

namespace PHPixie\Debug;

class Logger
{
    protected $builder;
    protected $items = array();
    
    public function __construct($builder)
    {
        $this->builder = $builder;
    }
    
    public function log($value, $shortDump = false, $backtraceOffset = 0)
    {
        $trace    = $this->getTrace(1, $backtraceOffset);
        $elements = $trace->elements();
        
        $this->items[] = $this->builder->loggerItem(
            $elements[0],
            $value,
            $shortDump
        );
    }
    
    public function trace($limit = null, $backtraceOffset = 0)
    {
        $trace    = $this->getTrace($limit, $backtraceOffset);
        $elements = $trace->elements();
        
        $this->items[] = $this->builder->loggerItem(
            $elements[0],
            $trace
        );
    }
    
    protected function getTrace($limit, $backtraceOffset)
    {
        $tracer  = $this->builder->tracer();
        return $tracer->backtrace($limit, 2+$backtraceOffset);
    }
    
    public function items()
    {
        return $this->items;
    }
    
    public function clearItems()
    {
        $this->items = array();
    }
    
    public function getAndClearItems()
    {
        $items = $this->items;
        $this->clearItems();
        return $items;
    }
    
    public function asString($withTraceArguments = true, $shortValueDump = null)
    {
        $string = '';
        foreach($this->items as $key => $item) {
            if($key > 0) {
                $string.="\n\n";
            }
            
            $string.= '#'.$key.' '.$item->asString($withTraceArguments, $shortValueDump);
        }
        
        return $string;
    }
    
    public function __toString()
    {
        return $this->asString();
    }

}