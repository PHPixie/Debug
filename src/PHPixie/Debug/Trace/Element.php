<?php

namespace PHPixie\Debug\Trace;

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
        $file,
        $line,
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
    
    public function argumentDumps()
    {
        $dumps = array();
        foreach($this->arguments as $argument) {
            $dumps[]= $this->dumper->dump($argument);
        }
        return $dumps;
    }
    
    public function lineContents($offset = 0)
    {
        $this->requireLineContents();
        $line = $this->line($offset);
        return rtrim($this->lineContents[$line-1], "\n\r");
    }
    
    public function context()
    {
        if($this->className !== null) {
            return $this->className.$this->type.$this->functionName;
        }
        
        if($this->functionName !==null) {
            return $this->functionName;
        }
        
        return null;
    }
    
    public function getNeighboringLines($maxAmount)
    {
        $amount = $maxAmount;
        $this->requireLineContents();
        $count = count($this->lineContents);

        if($amount > $count) {
            return range(1, $count);
        }
        
        $start = $this->line - (int) ($amount/2);
        
        if($start < 1) {
            return range(1, $amount);
        }
        
        if($start + $amount - 1 > $count) {
            
            return range($count - $amount + 1, $count);
        }
        
        $end = $start + $amount - 1;
        
        if($start > $end) {
            return array();
        }
        
        return range($start, $start + $amount - 1);
    }
    
    protected function requireLineContents()
    {
        if($this->lineContents === null) {
            $this->lineContents = file($this->file);
        }
    }
}