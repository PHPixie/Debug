<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\ErrorHandler
 */
class ErrorHandlerTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $errorHandler;
    
    public function setUp()
    {
        $this->builder      = $this->quickMock('\PHPixie\Debug\Builder');
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::register
     * @covers ::<protected>
     */
    public function testRegister()
    {
        $this->errorHandler = $this->errorHandlerMock(array(
            'registerErrorHandler',
            'registerExceptionHandler',
        ));
        
        $this->method($this->errorHandler, 'registerErrorHandler', null, array(), 0);
        $this->method($this->errorHandler, 'registerExceptionHandler', null, array(), 1);
        
        $this->errorHandler->register();
    }
    
    /**
     * @covers ::registerErrorHandler
     * @covers ::<protected>
     */
    public function testRegisterErrorHandler()
    {
        $this->errorHandler = $this->errorHandlerMock(array('setErrorHandler'));
        
        $handler = null;
        $this->method($this->errorHandler, 'setErrorHandler', function($callback) use(&$handler) {
            $handler = $callback;
        });
        
        $this->errorHandler->registerErrorHandler();
        
        $params = array(
            'severity' => 1,
            'code'     => 0,
            'message'  => 'test',
            'file'     => 'pixie',
            'line'     => 5
        );
        
        $exception = null;
        try{
            $handler(
                $params['severity'],
                $params['message'],
                $params['file'],
                $params['line']
            );
            
        }catch(\ErrorException $e) {
            $exception = $e;
        }
        
        foreach($params as $name => $value) {
            $method = 'get'.ucfirst($name);
            $this->assertSame($value, $exception->$method());
        }
    }
    
    /**
     * @covers ::registerExceptionHandler
     * @covers ::<protected>
     */
    public function testRegisterExceptionHandler()
    {
        $this->errorHandler = $this->errorHandlerMock(array(
            'setExceptionHandler',
            'handleException'
        ));
        
        $handler = null;
        $this->method($this->errorHandler, 'setExceptionHandler', function($callback) use(&$handler) {
            $handler = $callback;
        });
        
        $this->errorHandler->registerExceptionHandler();
        
        $exception = $this->quickMock('\stdClass');
        $this->method($this->errorHandler, 'handleException', null, array($exception), 0);
        
        $handler($exception);
    }
    
    /**
     * @covers ::setErrorHandler
     * @covers ::setExceptionHandler
     * @runInSeparateProcess
     */
    public function testSetHandlers()
    {
        $this->errorHandler = new \PHPixie\Debug\ErrorHandler($this->builder);
        $this->errorHandler->register();
    }
    
    protected function errorHandlerMock($methods = array())
    {
        return $this->quickMock('\PHPixie\Debug\ErrorHandler', $methods);
    }
}