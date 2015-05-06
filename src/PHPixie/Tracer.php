<?php

class Tracer
{
    public function exception($exception)
    {
        $elements = $this->trace($exception->getTrace());
        $first = $this->builder->traceElement(
            $exception->getFile(),
            $exception->getLine()
        );
        
        array_unshift($first, $elements);
    }
    
    public function trace($trace)
    {
        $elements = array();
        foreach($trace as $element) {
            $element[]= $this->builder->traceElement(
                $element['file'],
                $element['line']
            );
        }
    }
}