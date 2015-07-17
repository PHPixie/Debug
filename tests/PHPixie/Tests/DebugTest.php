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
    protected $handlers;
    protected $logger;
    protected $messages;
    protected $tracer;
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\Debug\Builder');
        foreach(array('dumper', 'handlers', 'logger', 'messages', 'tracer') as $name) {
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
     * @covers ::logger
     * @covers ::<protected>
     */
    public function testInstances()
    {
        $this->prepareDebug();
        foreach(array('builder', 'logger') as $name) {
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
        
        $this->method($this->handlers, 'register', null, array(false, true, true), 0);
        $this->debug->registerHandlers();
        
        $this->method($this->handlers, 'register', null, array(true, false, false), 0);
        $this->debug->registerHandlers(true, false, false);
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
        $this->assertSame("\ntest\n", $string);
        
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
        $this->assertSame("\ntest\n", $string);
        
        $this->method($this->messages, 'exception', 'test', array($exception, 3, 4), 0);
        $this->assertSame('test', $this->debug->exceptionMessage($exception, 3, 4, false));
    }
    
    /**
     * @covers ::exceptionTrace
     * @covers ::<protected>
     */
    public function testExceptionTrace()
    {
        $this->prepareDebug();
        
        $exception = $this->quickMock('\stdClass');
        
        $trace = $this->quickMock('\PHPixie\Debug\Tracer\Trace');
        $this->method($this->tracer, 'exceptionTrace', $trace, array($exception, 3), 0);
        
        $this->assertSame($trace, $this->debug->exceptionTrace($exception, 3));
        
        $trace = $this->quickMock('\PHPixie\Debug\Tracer\Trace');
        $this->method($this->tracer, 'exceptionTrace', $trace, array($exception, null), 0);
        
        $this->assertSame($trace, $this->debug->exceptionTrace($exception));
    }
    
    /**
     * @covers ::log
     * @covers ::<protected>
     */
    public function testLog()
    {
        $this->prepareDebug();
        
        $method = get_class($this->debug).'::log';
        
        $this->method($this->logger, 'log', null, array(5, false, 1), 0);
        call_user_func($method, 5);
        
        $this->method($this->logger, 'log', null, array(5, true, 1), 0);
        call_user_func($method, 5, true);
    }
    
    /**
     * @covers ::logTrace
     * @covers ::<protected>
     */
    public function testLogTrace()
    {
        $this->prepareDebug();
        
        $method = get_class($this->debug).'::logTrace';
        
        $this->method($this->logger, 'trace', null, array(5, 1), 0);
        call_user_func($method, 5);
        
        $this->method($this->logger, 'trace', null, array(null, 1), 0);
        call_user_func($method);
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
        $this->assertSame("\ntest\n", $string);
        
        $this->method($this->dumper, 'dump', 'test', array(5, true), 0);
        $this->assertSame('test', call_user_func($method, 5, true, false));
    }
    
    /**
     * @covers ::trace
     * @covers ::<protected>
     */
    public function testTrace()
    {
        $this->prepareDebug();
        
        $trace = $this->quickMock('\PHPixie\Debug\Tracer\Trace');
        $this->method($trace, '__toString', 'test', array());
        
        $method = get_class($this->debug).'::trace';
        
        $this->method($this->tracer, 'backtrace', $trace, array(null, 1), 0);
        
        ob_start();
        $result = call_user_func($method);
        $string = ob_get_clean();
        
        $this->assertSame($trace, $result);
        $this->assertSame("\ntest\n", $string);
        
        $this->method($this->tracer, 'backtrace', $trace, array(3, 2), 0);
        $this->assertSame($trace, call_user_func($method, 3, 1, false));
    }
    
    /**
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