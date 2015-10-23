<?php

namespace PHPixie\Debug;

class Handlers
{
    protected $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    public function register($shutdownLog = false, $exception = true, $error = true)
    {
        if($error) {
            $this->registerErrorHandler();
        }

        if($exception) {
            $this->registerExceptionHandler();
        }

        if($shutdownLog) {
            $this->registerShutdownLogHandler();
        }
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

    public function registerShutdownLogHandler()
    {
        $self = $this;
        $this->setShutdownHandler(function() use($self) {
            $self->handleShutdownLog();
        });
    }

    public function handleError($level, $message, $file, $line)
    {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    public function handleException($exception)
    {
        $messages = $this->builder->messages();
        echo "\n\n".$messages->exception($exception);
        echo "\n\n".$messages->log();
    }

    public function handleShutdownLog()
    {
        $messages = $this->builder->messages();
        echo "\n\n".$messages->log();
    }

    protected function setErrorHandler($callback)
    {
        set_error_handler($callback);
    }

    protected function setExceptionHandler($callback)
    {
        set_exception_handler($callback);
    }

    protected function setShutdownHandler($callback)
    {
        register_shutdown_function($callback);
    }
}
