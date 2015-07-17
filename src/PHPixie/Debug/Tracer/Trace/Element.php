<?php

namespace PHPixie\Debug\Tracer\Trace;

class Element
{
    protected $dumper;
    protected $file;
    protected $line;
    protected $class;
    protected $functionName;
    protected $args;
    protected $lineContents;
    
    public function __construct(
        $dumper,
        $file         = null,
        $line         = null,
        $functionName = null,
        $arguments    = null,
        $className    = null,
        $object       = null,
        $type         = null
    )
    {
        $this->dumper       = $dumper;
        $this->file         = $file;
        $this->line         = $line;
        $this->functionName = $functionName;
        $this->arguments    = $arguments;
        $this->className    = $className;
        $this->object       = $object;
        $this->type         = $type;
    }
    
    public function file()
    {
        return $this->file;
    }
    
    public function line($offset = 0)
    {
        if(!$this->lineAndFileAvailable()) {
            return null;
        }
        
        $line = $this->line + $offset;
        
        if($line < 1) {
            throw new \PHPixie\Debug\Exception("Invalid negative offset '$offset', line number must be larger than 0");
        }
        
        if($offset > 0) {
            $this->requireLineContents();
            $count = count($this->lineContents);
            if($line > $count) {
                throw new \PHPixie\Debug\Exception("Invalid offset '$offset', line number must not exceed line count {$count}");
            }
        }
        
        return $line;
    }
    
    public function functionName()
    {
        return $this->functionName;
    }
    
    public function arguments()
    {
        return $this->arguments;
    }
    
    public function className()
    {
        return $this->className;
    }
    
    public function object()
    {
        return $this->object;
    }
    
    public function type()
    {
        return $this->type;
    }
    
    public function shortArgumentDumps()
    {
        $dumps = array();
        foreach($this->arguments as $argument) {
            $dumps[]= $this->dumper->shortDump($argument);
        }
        return $dumps;
    }
    
    public function lineContents($offset = 0)
    {
        if(!$this->lineAndFileAvailable()) {
            return null;
        }
        
        $this->requireLineContents();
        $line = $this->line($offset);
        return rtrim($this->lineContents[$line-1], "\n\r");
    }
    
    public function context($withArguments = false)
    {
        if($this->functionName === null) {
            return null;
        }
        
        if($this->className !== null) {
            $context = $this->className.$this->type.$this->functionName;
            
        }else{
            $context = $this->functionName;
        }
        
        if($withArguments) {
            $shortArgumentDumps = $this->shortArgumentDumps();
            $context.='('.implode(', ', $shortArgumentDumps).')';
        }
        
        return $context;
    }
    
    public function getNeighboringOffsets($limit)
    {
        $offsets = $this->getNeighboringLines($limit);
        foreach($offsets as $key => $line) {
            $offsets[$key] = $line - $this->line;
        }
        
        return $offsets;
    }
    
    protected function getNeighboringLines($limit)
    {
        if(!$this->lineAndFileAvailable()) {
            return array();
        }
        
        $this->requireLineContents();
        $count = count($this->lineContents);

        if($limit > $count) {
            return range(1 , $count);
        }
        
        $start = $this->line - (int) ($limit/2);
        
        if($start < 1) {
            return range(1, $limit);
        }
        
        if($start + $limit - 1 > $count) {
            
            return range($count - $limit + 1, $count);
        }
        
        $end = $start + $limit - 1;
        
        if($start > $end) {
            return array();
        }
        
        return range($start, $start + $limit - 1);    
    }
    
    public function asString($withArguments = true, $withContext = true)
    {
        $string = $this->location();
        
        if($withContext) {
            $context = $this->context($withArguments);
            if($context !== null) {
                $string.= "\n    ".$context;
            }
        }
        
        return $string;
    }
    
    public function location()
    {
        if($this->file !== null) {
            $string = $this->file;
        }else{
            $string = '<unknown>';
        }
        
        if($this->line !== null) {
            $string.= ':'.$this->line;
        }   
        
        return $string;
    }
    
    public function __toString()
    {
        return $this->asString();
    }
    
    protected function lineAndFileAvailable()
    {
        return $this->line !== null && $this->file !== null;
    }
    
    protected function requireLineContents()
    {
        if($this->lineContents === null) {
            $this->lineContents = file($this->file);
        }
    }
    
}