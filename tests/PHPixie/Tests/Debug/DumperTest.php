<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\Dumper
 */
class DumperTest extends \PHPixie\Test\Testcase
{
    protected $dumper;
    
    public function setUp()
    {
        $this->dumper = new \PHPixie\Debug\Dumper();
    }
    
    /**
     * @covers ::dump
     * @covers ::<protected>
     */
    public function testDump()
    {
        $trace = $this->getTrace(array(
            array('pixie', 'trixie', null),
            array(null, 'trixie', 10),
        ));
        
        $sets = array(
            array('pixiefairystellablum', "'pixiefairystellablum'"),
            array(true, 'true'),
            array(false, 'false'),
            array(new \stdClass, print_r(new \stdClass, true)),
            array(array(1, 2), print_r(array(1, 2), true)),
            array(null, 'NULL'),
            array(5, '5'),
            array($trace, "pixie\ntrixie:10"),
        );
        
        foreach($sets as $set) {
            $this->assertSame($set[1], $this->dumper->dump($set[0]));
        }
        
        $sets = array(
            array('pixiefairystellablum', "'pixiefairyst...'"),
            array('pixie', "'pixie'"),
            array(true, 'true'),
            array(false, 'false'),
            array(new \stdClass, 'stdClass'),
            array(array(1, 2), "array[2]"),
            array(null, 'NULL'),
            array(5, '5'),
            array($trace, get_class($trace)),
        );
        
        foreach($sets as $set) {
            $this->assertSame($set[1], $this->dumper->dump($set[0], true));
            $this->assertSame($set[1], $this->dumper->shortDump($set[0]));
        }
    }
    
    protected function getTrace($elementData)
    {
        $trace = $this->quickMock('\PHPixie\Debug\Tracer\Trace');
        $elements = array();
        foreach($elementData as $data) {
            $element = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
            $this->method($element, 'context', $data[0], array());
            $this->method($element, 'file', $data[1], array());
            $this->method($element, 'line', $data[2], array());
            $elements[]= $element;
        }
        
        $this->method($trace, 'elements', $elements, array());
        return $trace;
    }
}