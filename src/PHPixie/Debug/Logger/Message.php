<?php

namespace PHPixie\Debug\Logger;

class Message
{
    protected $file;
    protected $line;
    protected $value;
    
    public function __construct($file, $line, $value)
    {
        $this->file = $file;
        $this->line = $line;
        $this->line = $value;
    }
}