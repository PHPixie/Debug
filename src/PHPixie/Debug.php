<?php

namespace PHPixie;

class Debug
{
    protected $builder;
    
    public function log($value, $shortDump = false)
    {
        $this->builder->logger->log($value, $shortDump, 1);
    }
    
    public function trace($limit)
    {
        $this->builder->logger->trace($limit);
    }
    
    public function registerHandlers()
    {
        $this->builder->errorHandler()->register();
    }
    
    public function loggedItemsDump()
    {
        return $this->builder->logger->itemsDump();
    }
}