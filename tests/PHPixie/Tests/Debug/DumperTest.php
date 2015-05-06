<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\Dumper
 */
class DumperTest extends \PHPixie\Test\Testcase
{
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testDump()
    {
        $sets = array(
            array(
                'configData' => array(
                    'showStringValues' => false
                ),
                'sets' => array(
                    array('test', 'string[4]'),
                    array(true, 'true'),
                    array(false, 'false'),
                    array($this, 'DumperTest'),
                    array(array(1, 2), 'array[2]'),
                    array(null, 'null'),
                    array(5, '5')
                )
            ),
            array(
                'configData' => array(
                    'showStringValues' => true
                ),
                'sets' => array(
                    array('test', "'test'")
                )
            )
        );
        
        foreach($sets as $set) {
            $slice = $this->quickMock('\PHPixie\Slice\Data');
            $this->method($slice, 'get', $set['configData']['showStringValues'], array('showStringValues', false), 0);
            $dumper = new \PHPixie\Debug\Dumper($slice);
            
            foreach($set['sets'] as $case) {
                $this->assertSame($case[1], $dumper->dump($case[0]));
            }
        }
    }
}