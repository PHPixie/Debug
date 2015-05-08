<?php

namespace PHPixie\Debug;

class ErrorHandler
{
    protected $builder;
    
    public function __construct($builder)
    {
        $this->builder = $builder;
    }
    
    public function register()
    {
        $this->registerErrorHandler();
        $this->registerExceptionHandler();
    }
    
    public function registerErrorHandler()
    {
        $self = $this;
        $this->setErrorHandler(function($level, $message, $file, $line) use($self) {
            $self->handleError($level, $message, $file, $line);
        });
    }
    
    public function registerExceptionHandler()
    {
        $self = $this;
        $this->setExceptionHandler(function($exception) use($self) {
            $self->handleException($exception);
        });
    }
    
    public function handleException($exception)
    {
        $dumper = $this->builder->dumper();
        $logger = $this->builder->logger();
        
        $string = $dumper->dump($exception);
        $itemsDump = $logger->itemsDump();
        if($itemsDump !== null) {
            $string.= "\n\nLogged items: $itemsDump";
        }
        
        echo $string;
    }
    
    protected function handleError($level, $message, $file, $line)
    {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
    
    protected function setErrorHandler($callback)
    {
        set_error_handler($callback);
    }
    
    protected function setExceptionHandler($callback)
    {
        set_exception_handler($callback);
    }
}