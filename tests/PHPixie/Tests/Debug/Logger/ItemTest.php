<?php

namespace PHPixie\Tests\Logger;

/**
 * @coversDefaultClass \PHPixie\Debug\Logger\Item
 */
class ItemTest extends \PHPixie\Test\Testcase
{
    protected $dumper;
    
    protected $traceElement;
    protected $value = 'pixie';
    protected $shortDumpByDefault = true;
    
    protected $item;
    
    public function setUp()
    {
        $this->dumper = $this->quickMock('\PHPixie\Debug\Dumper');
        $this->traceElement = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
        $this->item = $this->item();
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::traceElement
     * @covers ::<protected>
     */
    public function testTraceElement()
    {
        $this->assertSame($this->traceElement, $this->item->traceElement());
    }
    
    /**
     * @covers ::value
     * @covers ::<protected>
     */
    public function testValue()
    {
        $this->assertSame($this->value, $this->item->value());
    }
    
    /**
     * @covers ::valueDump
     * @covers ::<protected>
     */
    public function testValueDump()
    {
        $this->method($this->dumper, 'dump', 'fairy', array($this->value, true), 0);
        $this->assertSame('fairy',  $this->item->valueDump(true));
        
        $this->method($this->dumper, 'dump', 'fairy', array($this->value, true), 0);
        $this->assertSame('fairy',  $this->item->valueDump());
        
        $this->method($this->dumper, 'dump', 'fairy', array($this->value, false), 0);
        $this->assertSame('fairy',  $this->item->valueDump(false));
        
        $this->item = new \PHPixie\Debug\Logger\Item(
            $this->dumper,
            $this->traceElement,
            $this->value
        );
        $this->method($this->dumper, 'dump', 'fairy', array($this->value, false), 0);
        $this->assertSame('fairy',  $this->item->valueDump());
    }
    
    /**
     * @covers ::asString
     * @covers ::__toString
     * @covers ::<protected>
     */
    public function testAsString()
    {
        $string = $this->prepareAsString();
        $this->assertSame($string, $this->item->asString());
        
        $string = $this->prepareAsString(false);
        $this->assertSame($string, $this->item->asString(false));
        
        $string = $this->prepareAsString(false, false);
        $this->assertSame($string, $this->item->asString(false, false));
        
        $string = $this->prepareAsString();
        $this->assertSame($string, (string) $this->item);
    }
    
    protected function prepareAsString($withTraceArguments = true, $shortValueDump = null)
    {
        if($shortValueDump === null) {
            $shortValueDump = $this->shortDumpByDefault;
        }
        
        $this->method($this->traceElement, 'asString', 'pixie', array($withTraceArguments), 0);
        $this->method($this->dumper, 'dump', 'fairy', array($this->value, $shortValueDump), 0);
        
        return "pixie\nfairy";
    }
    
    protected function item()
    {
        return new \PHPixie\Debug\Logger\Item(
            $this->dumper,
            $this->traceElement,
            $this->value,
            $this->shortDumpByDefault
        );
    }
}