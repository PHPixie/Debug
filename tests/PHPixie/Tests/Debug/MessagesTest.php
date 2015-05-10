<?php

namespace PHPixie\Tests\Debug;

/**
 * @coversDefaultClass \PHPixie\Debug\Messages
 */
class MessagesTest extends \PHPixie\Test\Testcase
{
    protected $builder;
    protected $messages;
    
    protected $dumper;
    protected $logger;
    protected $tracer;
    
    public function setUp()
    {
        $this->builder  = $this->quickMock('\PHPixie\Debug\Builder');
        $this->messages = new \PHPixie\Debug\Messages($this->builder);
        
        foreach(array('dumper', 'logger', 'tracer') as $name) {
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
        
    }
    
    /**
     * @covers ::log
     * @covers ::<protected>
     */
    public function testLog()
    {
        $string = $this->prepareLog();
        $this->assertSame($string, $this->messages->log());
        
        $string = $this->prepareLog(false, false, true);
        $this->assertSame($string, $this->messages->log(false, false, true));
    }
    
    protected function prepareLog($withTitle = true, $withTraceArguments = true, $shortValueDump = null)
    {
        $string = '';
        if($withTitle) {
            $string.= "Logged items:\n\n";
        }
        
        $this->method($this->logger, 'asString', 'logString', array($withTraceArguments, $shortValueDump), 0);
        $string.='logString';
        
        return $string;
    }
    
    /**
     * @covers ::exception
     * @covers ::<protected>
     */
    public function testException()
    {
        $exception = $this->quickMock('\stdClass');
        
        $string = $this->prepareException($exception);
        $this->assertSame($string, $this->messages->exception($exception));
        
        $string = $this->prepareException($exception, 5, 7);
        $this->assertSame($string, $this->messages->exception($exception, 5, 7));
        
        $string = $this->prepareException($exception, null, 0);
        $this->assertSame($string, $this->messages->exception($exception, null, 0));

        $string = $this->prepareException($exception, 0);
        $this->assertSame($string, $this->messages->exception($exception, 0));
    }
    
    protected function prepareException($exception, $backtraceLimit = null, $neighboringLines = 5)
    {
        $string = 'exception';
        
        $this->method($this->dumper, 'dump', 'exception', array($exception), 0);
        
        if($backtraceLimit !== 0) {
            $trace = $this->quickMock('\PHPixie\Debug\Tracer\Trace');
            $this->method($this->tracer, 'exceptionTrace', $trace, array($exception, $backtraceLimit), 0);
            
            $traceAt = 0;
            if($neighboringLines > 0) {
                $element = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
                $this->method($trace, 'elements', array($element), array(), $traceAt++);
                $string.= "\n\n".$this->prepareNeighboringLines($element, $neighboringLines);
            }
            
            $this->method($trace, 'asString', 'trace', array(), $traceAt++);
            $string.= "\n\ntrace";
        }
        
        return $string;
    }
    
    /**
     * @covers ::neighboringLines
     * @covers ::<protected>
     */
    public function testNeighboringLines()
    {
        $element = $this->quickMock('\PHPixie\Debug\Tracer\Trace\Element');
        
        $string = $this->prepareNeighboringLines($element, 3);
        $this->assertSame($string, $this->messages->neighboringLines($element, 3));
        
        $this->method($element, 'getNeighboringOffsets', array(), array(3), 0);
        $this->assertSame('', $this->messages->neighboringLines($element, 3));
    }
    
    protected function prepareNeighboringLines($element, $limit)
    {
        $offsetLines = array(
            -1 => 'pixie',
             0 => 'trixie',
             1 => 'stella'
        );
        
        $this->method($element, 'getNeighboringOffsets', array_keys($offsetLines), array($limit), 0);
        $this->method($element, 'line', function($offset) {
            return 99 + $offset;
        });
        
        $this->method($element, 'lineContents', function($offset) use($offsetLines) {
            return $offsetLines[$offset];
        });
        
        return "98  pixie\n>>> trixie\n100 stella";
    }
}