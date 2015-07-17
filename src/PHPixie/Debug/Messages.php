<?php

namespace PHPixie\Debug;

class Messages
{
    protected $builder;
    
    public function __construct($builder)
    {
        $this->builder = $builder;
    }
    
    public function exception($exception, $backtraceLimit = null, $neighboringLines = 5)
    {
        $dumper = $this->builder->dumper();
        $tracer = $this->builder->tracer();
        
        $string = $dumper->dump($exception);
        
        if($backtraceLimit !== 0) {
            $trace  = $tracer->exceptionTrace($exception, $backtraceLimit);
            
            if($neighboringLines > 0) {
                $elements = $trace->elements();
                $string.= "\n\n".$this->neighboringLines($elements[0], $neighboringLines);
            }
            
            $string.= "\n\n".$trace->asString();
        }
        
        return $string;
    }
    
    public function log($withTitle = true, $withTraceArguments = true, $shortValueDump = null)
    {
        $string = '';
        if($withTitle) {
            $string.= "Logged items:\n\n";
        }
        
        $logger = $this->builder->logger();
        $string.= $logger->asString($withTraceArguments, $shortValueDump);
        
        return $string;
    }
    
    public function neighboringLines($traceElement, $amount = 5)
    {
        $offsets = $traceElement->getNeighboringOffsets($amount);
        if(count($offsets) === 0) {
            return '';
        }
        
        $string = '';
        $pad = strlen($traceElement->line(end($offsets)));
        foreach($offsets as $key => $offset) {
            if($offset !== 0) {
                $prefix = $traceElement->line($offset);
                $prefix = str_pad($prefix, $pad);
            }else{
                $prefix = str_pad('', $pad, '>');
            }
            
            if($key > 0) {
                $string.= "\n";
            }
            $string.= $prefix.' '.$traceElement->lineContents($offset);
        }
        
        return $string;
    }
}