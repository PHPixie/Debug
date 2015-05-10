<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\Builder
 */
class BuilderTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    
    public function setUp()
    {
        $this->builder = new \PHPixie\Debug\Builder();
    }
    
    /**
     * @covers ::dumper
     * @covers ::<protected>
     */
    public function testDumper()
    {
        $dumper = $this->builder->dumper();
        
        $this->assertInstance($dumper, '\PHPixie\Debug\Dumper');
        $this->assertSame($dumper, $this->builder->dumper());
    }
    
    /**
     * @covers ::handlers
     * @covers ::<protected>
     */
    public function testHandlers()
    {
        $handlers = $this->builder->handlers(array(
            'builder' => $this->builder
        ));
        
        $this->assertInstance($handlers, '\PHPixie\Debug\Handlers');
        $this->assertSame($handlers, $this->builder->handlers());
    }
    
    
    /**
     * @covers ::logger
     * @covers ::<protected>
     */
    public function testLogger()
    {
        $logger = $this->builder->logger();
        
        $this->assertInstance($logger, '\PHPixie\Debug\Logger', array(
            'builder' => $this->builder
        ));
        $this->assertSame($logger, $this->builder->logger());
    }
    
    /**
     * @covers ::messages
     * @covers ::<protected>
     */
    public function testMessages()
    {
        $messages = $this->builder->messages();
        
        $this->assertInstance($messages, '\PHPixie\Debug\Messages', array(
            'builder' => $this->builder
        ));
        $this->assertSame($messages, $this->builder->messages());
    }
    
    /**
     * @covers ::tracer
     * @covers ::<protected>
     */
    public function testTracer()
    {
        $tracer = $this->builder->tracer();
        
        $this->assertInstance($tracer, '\PHPixie\Debug\Tracer', array(
            'builder' => $this->builder
        ));
        $this->assertSame($tracer, $this->builder->tracer());
    }
    
    /**
     * @covers ::trace
     * @covers ::<protected>
     */
    public function testTrace()
    {
        $elements = array(
            $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element')
        );
        
        $trace = $this->builder->trace($elements);
        $this->assertInstance($trace, '\PHPixie\Debug\Tracer\Trace', array(
            'elements' => $elements
        ));
        
        $trace = $this->builder->trace();
        $this->assertInstance($trace, '\PHPixie\Debug\Tracer\Trace', array(
            'elements' => array()
        ));
    }
    
    /**
     * @covers ::loggerItem
     * @covers ::<protected>
     */
    public function testLoggerItem()
    {
        $element = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
        
        $item = $this->builder->loggerItem($element, 5);
        $this->assertInstance($item, '\PHPixie\Debug\Logger\Item', array(
            'dumper'             => $this->builder->dumper(),
            'traceElement'       => $element,
            'value'              => 5,
            'shortDumpByDefault' => false
        ));
        
        $item = $this->builder->loggerItem($element, 5, true);
        $this->assertInstance($item, '\PHPixie\Debug\Logger\Item', array(
            'shortDumpByDefault' => true
        ));
    }
    
    /**
     * @covers ::traceElement
     * @covers ::<protected>
     */
    public function testTraceElement()
    {
        $params = array(
            'file'         => 'pixie',
            'line'         => 3,
            'functionName' => 'find',
            'arguments'    => array(1, 2),
            'className'    => 'Pixie',
            'object'       => new \stdClass,
            'type'         => '->'
        );
        
        $element = call_user_func_array(array($this->builder, 'traceElement'), $params);
        $this->assertInstance($element, '\PHPixie\Debug\Tracer\Trace\Element', $params);
        
        $params = array_fill_keys(array_keys($params), null);
        $element = $this->builder->traceElement();
        $this->assertInstance($element, '\PHPixie\Debug\Tracer\Trace\Element', $params);
    }
}    