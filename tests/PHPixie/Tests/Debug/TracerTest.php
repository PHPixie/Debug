<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\Tracer
 */
class TracerTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $tracer;
    
    protected $traceFields = array(
        'file',
        'line',
        'function',
        'args',
        'class',
        'object',
        'type'
    );
    
    public function setUp()
    {
        $this->builder = $this->quickMock('\PHPixie\Debug\Builder');
        $this->tracer  = $this->getMock(
            '\PHPixie\Debug\Tracer',
            array(
                'debugBacktrace'
            ),
            array(
                $this->builder
            )
        );
    }
    
    /**
     * @covers ::__construct
     * @covers ::<protected>
     */
    public function testConstruct()
    {
        
    }
    
    /**
     * @covers ::exceptionTrace
     * @covers ::<protected>
     */
    public function testExceptionTrace()
    {
        $backtrace = array(
            array(
                'file'     => 'pixie',
                'line'     => 3
            ),
            array(
                'class'    => 'PHPixie\Debug\Handlers'
            ),
            array(
                'file'     => 'fairy',
                'line'     => 5
            )
        );
        
        $exception = $this->quickMock('\stdClass', array('getFile', 'getLine', 'getTrace'));
        $this->method($exception, 'getFile', 'pixie', array(), 0);
        $this->method($exception, 'getLine', 3, array(), 1);
        
        $builderAt = 0;
        $first = $this->prepareTraceElement(array('pixie', 3), $builderAt++);
        
        $this->method($exception, 'getTrace', $backtrace, array(), 2);
        $elements = $this->prepareTraceElements($backtrace, $builderAt);
        
        array_unshift($elements, $first);
        
        $trace = $this->getTrace();
        $this->method($this->builder, 'trace', $trace, array($elements), $builderAt++);
        
        $this->assertSame($trace, $this->tracer->exceptionTrace($exception));
    }
    
    /**
     * @covers ::backtrace
     * @covers ::<protected>
     */
    public function testBacktrace()
    {
        $this->backtraceTest();
        $this->backtraceTest(true);
        $this->backtraceTest(true, true);
    }
    
    /**
     * @covers ::debugBacktrace
     * @covers ::<protected>
     */
    public function testBacktracing()
    {
        $this->builder = new \PHPixie\Debug\Builder;
        $this->tracer = new \PHPixie\Debug\Tracer($this->builder);
        $elements = $this->tracer->backtrace()->elements();
        
        $element = $elements[0];
        $this->assertSame(__FILE__, $element->file());
        $this->assertSame(103, $element->line());
    }
    
    protected function backtraceTest($withLimit = false, $withOffset = false)
    {
        $backtrace = array(
            array(),
            array(
                'file'     => 'pixie',
                'line'     => 3
            ),
            array(
                'file'     => 'pixie',
                'line'     => 3,
                'function' => 'find',
                'args'     => array(1, 2),
                'class'    => 'Pixie',
                'object'   => new \stdClass,
                'type'     => '->'
            )
        );
        
        $builderAt = 0;
        $elements = array();
        
        $limit = $withLimit ? 1 : null;
        $offset = $withOffset ? 1 : 0;
        
        $this->method($this->tracer, 'debugBacktrace', $backtrace, array(), 0);
        $data = array_slice($backtrace, $offset+1, $limit);
        $elements = $this->prepareTraceElements($data, $builderAt);
        
        $trace = $this->getTrace();
        $this->method($this->builder, 'trace', $trace, array($elements), $builderAt++);
        
        $this->assertSame($trace, $this->tracer->backtrace($limit, $offset));
    }
    
    protected function prepareTraceElements($data, &$builderAt = 0)
    {
        $elements = array();
        foreach($data as $params) {
            $fields = array_fill_keys($this->traceFields, null);
            $fields = array_merge($fields, $params);
            if($fields['class'] === 'PHPixie\Debug\Handlers') {
                continue;
            }
            $elements[]= $this->prepareTraceElement($fields, $builderAt++);
        }
        
        return $elements;
    }
    
    protected function prepareTraceElement($params, $builderAt = 0)
    {
        $element = $this->getTraceElement();
        $this->method($this->builder, 'traceElement', $element, $params, $builderAt);
        return $element;
    }
    
    protected function getTrace()
    {
        return $this->quickMock('\PHPixie\Debug\Tracer\Trace');
    }
    
    protected function getTraceElement()
    {
        return $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
    }
}