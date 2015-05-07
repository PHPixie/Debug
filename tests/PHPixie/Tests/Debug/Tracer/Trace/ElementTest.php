<?php

namespace PHPixie\Tests\Tracer\Trace;

/**
 * @coversDefaultClass \PHPixie\Debug\Tracer\Trace\Element
 */
class ElementTest extends \PHPixie\Test\Testcase
{
    protected $dumper;
    
    protected $file;
    protected $line         = 3;
    protected $functionName = 'find';
    protected $arguments    = array(1, 2);
    protected $className    = 'Pixie';
    protected $object;
    protected $type         = '->';
    
    protected $element;
    
    public function setUp()
    {
        $this->dumper = $this->quickMock('\PHPixie\Debug\Dumper');
        
        $this->file   = tempnam(sys_get_temp_dir(), 'debug_element_test');
        $this->object = new \stdClass;
        
        $this->element = $this->element();
        
        file_put_contents($this->file, implode("\n", range('a', 'f')));
    }
    
    public function tearDown()
    {
        if(file_exists($this->file)) {
            unlink($this->file);
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
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testGetters()
    {
        $this->assertGetters(
            $this->file,
            $this->line,
            $this->functionName,
            $this->arguments,
            $this->className,
            $this->object,
            $this->type
        );
    }
    
    /**
     * @covers ::<public>
     * @covers ::<protected>
     */
    public function testDefaults()
    {
        $this->element = new \PHPixie\Debug\Tracer\Trace\Element(
            $this->dumper
        );
        
        $this->assertGetters();
    }
    
    /**
     * @covers ::line
     * @covers ::<protected>
     */
    public function testLine()
    {
        $this->assertSame(3, $this->element->line());
        $this->assertSame(1, $this->element->line(-2));
        $this->assertSame(5, $this->element->line(2));
        
        $element = $this->element;
        
        $this->assertException(function() use($element) {
            $element->line(-3);
        }, '\PHPixie\Debug\Exception');
        
        $this->assertException(function() use($element) {
            $element->line(4);
        }, '\PHPixie\Debug\Exception');
    }
    
    /**
     * @covers ::lineContents
     * @covers ::<protected>
     */
    public function testLineContents()
    {
        $this->assertSame("c", $this->element->lineContents());
        $this->assertSame("a", $this->element->lineContents(-2));
        $this->assertSame("e", $this->element->lineContents(2));
    }
    
    /**
     * @covers ::getNeighboringLines
     * @covers ::<protected>
     */
    public function testGetNeighboringLines()
    {
        $element = $this->element;
        $this->assertSame(array(2, 3, 4), $element->getNeighboringLines(3));
        $this->assertSame(array(3), $element->getNeighboringLines(1));
        $this->assertSame(array(1, 2, 3, 4, 5, 6), $element->getNeighboringLines(10));
        $this->assertSame(array(), $element->getNeighboringLines(0));
        $this->assertSame(array(), $element->getNeighboringLines(-1));
        
        $this->line = 2;
        $this->assertSame(array(1, 2, 3, 4), $this->element()->getNeighboringLines(4));
        
        $this->line = 5;
        $this->assertSame(array(3, 4, 5, 6), $this->element()->getNeighboringLines(4));
    }
    
    /**
     * @covers ::shortArgumentDumps
     * @covers ::<protected>
     */
    public function testShortArgumentDumps()
    {
        $dumps = $this->prepareShortArgumentDumps();
        $this->assertSame($dumps, $this->element->shortArgumentDumps());
    }
    
    /**
     * @covers ::context
     * @covers ::<protected>
     */
    public function testContext()
    {
        $this->assertSame('Pixie->find', $this->element->context());
        
        $dumps = $this->prepareShortArgumentDumps();
        $context = 'Pixie->find('.implode(', ', $dumps).')';
        $this->assertSame($context, $this->element->context(true));
        
        $this->className = null;
        $this->assertSame('find', $this->element()->context());
        
        $this->functionName = null;
        $element = $this->element();
        $this->assertSame(null, $element->context());
        $this->assertSame(null, $element->context(true));
    }
    
    /**
     * @covers ::line
     * @covers ::lineContents
     * @covers ::getNeighboringLines
     * @covers ::<protected>
     */
    public function testLineOrFileUnavailable()
    {
        $elements = array();
        
        $this->file = null;
        $elements[] = $this->element();
        
        $this->line = null;
        $this->file = 'pixie';
        $elements[] = $this->element();
        
        foreach($elements as $element) {
            $this->assertSame(null, $element->line());
            $this->assertSame(null, $element->lineContents());
            $this->assertSame(array(), $element->getNeighboringLines(1));
        }
    }
    
    protected function prepareShortArgumentDumps()
    {
        $dumps = array();
        
        foreach($this->arguments as $key => $argument) {
            $dump = 'd'.$key;
            $this->method($this->dumper, 'shortDump', $dump, array($argument), $key);
            $dumps[]=$dump;
        }
        
        return $dumps;
    }
    
    protected function assertGetters(
        $file         = null,
        $line         = null,
        $functionName = null,
        $arguments    = null,
        $className    = null,
        $object       = null,
        $type         = null
    )
    {
        foreach(get_defined_vars() as $method => $value) {
            $this->assertSame($value, $this->element->$method());
        }
    }
    
    protected function element()
    {
        return new \PHPixie\Debug\Tracer\Trace\Element(
            $this->dumper,
            $this->file,
            $this->line,
            $this->functionName,
            $this->arguments,
            $this->className,
            $this->object,
            $this->type
        );
    }
}