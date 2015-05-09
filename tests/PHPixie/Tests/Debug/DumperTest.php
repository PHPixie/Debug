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
     * @covers ::shortDump
     * @covers ::<protected>
     */
    public function testDump()
    {
        $trace = $this->quickMock('\PHPixie\Debug\Tracer\Trace');
        $this->method($trace, 'asString', 'pixie', array());
        
        $exception = new \Exception('test');
        
        $sets = array(
            array('pixiefairystellablum', "'pixiefairystellablum'"),
            array(true, 'true'),
            array(false, 'false'),
            array(new \stdClass, print_r(new \stdClass, true)),
            array(array(1, 2), print_r(array(1, 2), true)),
            array(null, 'NULL'),
            array(5, '5'),
            array($trace, "pixie"),
            array($exception, "Exception: test"),
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
            array($exception, "Exception"),
        );
        
        foreach($sets as $set) {
            $this->assertSame($set[1], $this->dumper->dump($set[0], true));
            $this->assertSame($set[1], $this->dumper->shortDump($set[0]));
        }
    }
}