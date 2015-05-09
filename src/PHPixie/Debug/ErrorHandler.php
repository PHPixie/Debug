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
    
    protected function handleError($level, $message, $file, $line)
    {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }
    
    protected function handleException($exception)
    {
        $messages = $this->builder->messages();
        echo $messages->exception($exception);
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