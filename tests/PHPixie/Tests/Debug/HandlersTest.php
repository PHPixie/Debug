<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\Handlers
 */
class HandlersTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $handlers;
    
    protected $messages;
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\Debug\Builder');
        
        $this->messages = $this->quickMock('\PHPixie\Debug\Messages');
        $this->method($this->builder, 'messages', $this->messages, array());
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        $this->handlers = new \PHPixie\Debug\Handlers($this->builder);
    }
    
    /**
     * @covers ::register
     * @covers ::<protected>
     */
    public function testRegister()
    {
        $this->handlers = $this->handlersMock(array(
            'registerErrorHandler',
            'registerExceptionHandler',
            'registerShutdownLogHandler',
        ));
        
        $this->method($this->handlers, 'registerErrorHandler', null, array(), 'once');
        $this->method($this->handlers, 'registerExceptionHandler', null, array(), 'once');
        $this->handlers->register();
        
        $this->method($this->handlers, 'registerShutdownLogHandler', null, array(), 'once');
        $this->handlers->register(true, false, false);

    }
    
    /**
     * @covers ::registerErrorHandler
     * @covers ::<protected>
     */
    public function testRegisterErrorHandler()
    {
        $this->handlers = $this->handlersMock(array('setErrorHandler'));
        
        $handler = null;
        $this->method($this->handlers, 'setErrorHandler', function($callback) use(&$handler) {
            $handler = $callback;
        });
        
        $this->handlers->registerErrorHandler();
        
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
        $this->handlers = $this->handlersMock(array('setExceptionHandler'));
        
        $handler = null;
        $this->method($this->handlers, 'setExceptionHandler', function($callback) use(&$handler) {
            $handler = $callback;
        });
        
        $this->handlers->registerExceptionHandler();
        
        $exception = $this->quickMock('\stdClass');
        
        $this->method($this->messages, 'exception', 'pixie', array($exception), 0);
        $this->method($this->messages, 'log', 'trixie', array(), 1);
        
        ob_start();
        $handler($exception);
        $string = ob_get_clean();
        $this->assertSame("\n\npixie\n\ntrixie", $string);
    }
    
    /**
     * @covers ::registerShutdownLogHandler
     * @covers ::<protected>
     */
    public function testRegisterShutdownLogHandler()
    {
        $this->handlers = $this->handlersMock(array('setShutdownHandler'));
        
        $handler = null;
        $this->method($this->handlers, 'setShutdownHandler', function($callback) use(&$handler) {
            $handler = $callback;
        });
        
        $this->handlers->registerShutdownLogHandler();
        
        $this->method($this->messages, 'log', 'pixie', array(), 0);
        
        ob_start();
        $handler();
        $string = ob_get_clean();
        $this->assertSame("\n\npixie", $string);
    }
    
    /**
     * @covers ::setErrorHandler
     * @covers ::setExceptionHandler
     * @covers ::setShutdownHandler
     * @runInSeparateProcess
     */
    public function testSetHandlers()
    {
        $builder = new \PHPixie\Debug\Builder();
        $this->handlers = new \PHPixie\Debug\Handlers($builder);
        $this->handlers->register(true);
    }
    
    protected function handlersMock($methods = array())
    {
        return $this->getMock('\PHPixie\Debug\Handlers', $methods, array($this->builder));
    }
}