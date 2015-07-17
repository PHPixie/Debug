<?php

namespace PHPixie\Tests\Tracer;

/**
 * @coversDefaultClass \PHPixie\Debug\Tracer\Trace
 */
class TraceTest extends \PHPixie\Test\Testcase
{
    protected $elements = array();
    protected $trace;
    
    public function setUp()
    {
        for($i=0; $i<3; $i++) {
            $this->elements[] = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
        }
        
        $this->trace = new \PHPixie\Debug\Tracer\Trace($this->elements);
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
    
    }
    
    /**
     * @covers ::elements
     * @covers ::<protected>
     */
    public function testElements()
    {
        $this->assertSame($this->elements, $this->trace->elements());
    }
    
    /**
     * @covers ::asString
     * @covers ::__toString
     * @covers ::<protected>
     */
    public function testAsString()
    {
        $string = $this->prepareElementStrings(true);
        $this->assertSame($string, $this->trace->asString());
        
        $string = $this->prepareElementStrings(false);
        $this->assertSame($string, $this->trace->asString(false));
        
        $string = $this->prepareElementStrings(true);
        $this->assertSame($string, (string) $this->trace);
    }
    
    protected function prepareElementStrings($withArguments = true)
    {
        $elementStrings = array();
        
        foreach($this->elements as $key => $element) {
            $elementString = "s$key";
            $this->method($element, 'asString', $elementString, array(), 0);
            $elementStrings[]= '#'.$key.' '.$elementString;
        }
        
        return implode("\n", $elementStrings);
    }
}