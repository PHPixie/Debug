<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\Logger
 */
class LoggerTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $logger;
    
    protected $tracer;
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\Debug\Builder');
        $this->logger  = new \PHPixie\Debug\Logger(
            $this->builder
        );
        
        $this->tracer = $this->quickMock('\PHPixie\Debug\Tracer');
        $this->method($this->builder, 'tracer', $this->tracer, array());
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::log
     * @covers ::<protected>
     */
    public function testLog()
    {
        $items = array();
        
        $items[]= $this->prepareItem(5);
        $this->logger->log(5);
        
        $items[]= $this->prepareItem(5, true);
        $this->logger->log(5, true);
        
        $items[]= $this->prepareItem(5, true, 2);
        $this->logger->log(5, true, 2);

        
        $this->assertSame($items, $this->logger->items());
    }
    
    /**
     * @covers ::items
     * @covers ::clearItems
     * @covers ::getClearItems
     * @covers ::<protected>
     */
    public function testItems()
    {
        $item = $this->prepareItem(5);
        $this->logger->log(5);

        $this->assertSame(array($item), $this->logger->items());
        
        $this->logger->clearItems();
        $this->assertSame(array(), $this->logger->items());
        
        $item = $this->prepareItem(5);
        $this->logger->log(5);

        $this->assertSame(array($item), $this->logger->getAndClearItems());
        $this->assertSame(array(), $this->logger->items());
    }
    
    /**
     * @covers ::trace
     * @covers ::<protected>
     */
    public function testTrace()
    {
        $items = array();
        
        $items[]= $this->prepareTrace();
        $this->logger->trace();
        
        $items[]= $this->prepareTrace(2);
        $this->logger->trace(2);
        
        $items[]= $this->prepareTrace(2, 1);
        $this->logger->trace(2, 1);

        $this->assertSame($items, $this->logger->items());
    }
    
    protected function prepareTrace($limit = null, $backtraceOffset = 0)
    {
        $trace = $this->prepareGetTrace($limit, $backtraceOffset);
        $traceElement = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
        $this->method($trace, 'elements', array($traceElement), array(), 0);
        
        $item = $this->getItem();
        $this->method($this->builder, 'loggerItem', $item, array($traceElement, $trace), 1);
        return $item;
    }
    
    protected function prepareItem($value, $shortDump = false, $backtraceOffset = 0)
    {
        $trace = $this->prepareGetTrace(1, $backtraceOffset);
        $traceElement = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
        $this->method($trace, 'elements', array($traceElement), array(), 0);
        
        $item = $this->getItem();
        $this->method($this->builder, 'loggerItem', $item, array($traceElement, $value, $shortDump), 1);
        return $item;
    }
    
    protected function prepareGetTrace($limit, $offset = 0)
    {
        $trace = $this->quickMock('\PHPixie\Debug\Tracer\Trace');      
        $this->method($this->tracer, 'backtrace', $trace, array($limit, $offset+2), 0);
        return $trace;
    }
    
    protected function getItem()
    {
        return $this->quickMock('\PHPixie\Debug\Logger\Item');
    }
}