<?php

namespace PHPixie\Tests;

/**
 * @coversDefaultClass \PHPixie\Debug
 */
class DebugTest extends \PHPixie\Test\Testcase
{
    protected $debug;
    
    protected $builder;
    
    protected $dumper;
    protected $errorHandler;
    protected $logger;
    protected $messages;
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\Debug\Builder');
        foreach(array('dumper', 'errorHandler', 'logger', 'messages') as $name) {
            $this->$name  = $this->quickMock('\PHPixie\Debug\\'.ucfirst($name));
            $this->method($this->builder, $name, $this->$name, array());
        }
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        $this->prepareDebug();
    }
    
    /**
     * @covers ::builder
     * @covers ::dumper
     * @covers ::logger
     * @covers ::<protected>
     */
    public function testInstances()
    {
        $this->prepareDebug();
        foreach(array('builder', 'dumper', 'logger') as $name) {
            $this->assertSame($this->$name, $this->debug->$name());
        }
    }
    
    /**
     * @covers ::registerHandlers
     * @covers ::<protected>
     */
    public function testRegisterHandlers()
    {
        $this->prepareDebug();
        $this->method($this->errorHandler, 'register', null, array(), 0);
        $this->debug->registerHandlers();
    }
    
    /**
     * @covers ::dumpLog
     * @covers ::<protected>
     */
    public function testDumpLog()
    {
        $this->prepareDebug();
        
        $this->method($this->messages, 'log', 'test', array(true, true, null), 0);
        
        ob_start();
        $log = $this->debug->dumpLog();
        $string = ob_get_clean();
        
        $this->assertSame('test', $log);
        $this->assertSame('test', $string);
        
        $this->method($this->messages, 'log', 'test', array(false, false, true), 0);
        $this->assertSame('test', $this->debug->dumpLog(false, false, true, false));
    }
    
    /**
     * @covers ::exceptionMessage
     * @covers ::<protected>
     */
    public function testExceptionMessage()
    {
        $this->prepareDebug();
        
        $exception = $this->quickMock('\stdClass');
        $this->method($this->messages, 'exception', 'test', array($exception, null, 5), 0);
        
        ob_start();
        $message = $this->debug->exceptionMessage($exception);
        $string = ob_get_clean();
        
        $this->assertSame('test', $message);
        $this->assertSame('test', $string);
        
        $this->method($this->messages, 'exception', 'test', array($exception, 3, 4), 0);
        $this->assertSame('test', $this->debug->exceptionMessage($exception, 3, 4, false));
    }
    
    /**
     * @covers ::log
     * @covers ::<protected>
     */
    public function testLog()
    {
        $this->prepareDebug();
        
        $method = get_class($this->debug).'::log';
        
        $this->method($this->logger, 'log', null, array(5, false), 0);
        call_user_func($method, 5);
        
        $this->method($this->logger, 'log', null, array(5, true), 0);
        call_user_func($method, 5, true);
    }
    
    /**
     * @covers ::trace
     * @covers ::<protected>
     */
    public function testTrace()
    {
        $this->prepareDebug();
        
        $method = get_class($this->debug).'::trace';
        
        $this->method($this->logger, 'trace', null, array(5), 0);
        call_user_func($method, 5);
    }
    
    /**
     * @covers ::dump
     * @covers ::<protected>
     */
    public function testDump()
    {
        $this->prepareDebug();
        
        $method = get_class($this->debug).'::dump';
        
        $this->method($this->dumper, 'dump', 'test', array(5, false), 0);
        
        ob_start();
        $dump = call_user_func($method, 5);
        $string = ob_get_clean();
        
        $this->assertSame('test', $dump);
        $this->assertSame('test', $string);
        
        $this->method($this->dumper, 'dump', 'test', array(5, true), 0);
        $this->assertSame('test', call_user_func($method, 5, true, false));
    }
    
    /**
     * @covers ::dump
     * @covers ::<protected>
     * @runInSeparateProcess
     */
    public function testNotBuiltException()
    {
        $this->assertException(function() {
            \PHPixie\Debug::dump(5);
        }, '\PHPixie\Debug\Exception');
    }
    
    /**
     * @covers ::buildBuilder
     * @covers ::<protected>
     */
    public function testBuildBuilder()
    {
        $this->debug = new \PHPixie\Debug();
        
        $builder = $this->debug->builder();
        $this->assertInstance($builder, '\PHPixie\Debug\Builder', array());
    }
    
    protected function prepareDebug()
    {
        $this->debug = $this->getMockBuilder('\PHPixie\Debug')
            ->setMethods(array('buildBuilder'))
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->method($this->debug, 'buildBuilder', $this->builder, array(), 0);
        $this->debug->__construct();
    }
}