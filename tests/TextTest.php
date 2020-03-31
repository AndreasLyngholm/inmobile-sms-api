<?php

namespace Inmobile;


use Inmobile\Exceptions\InvalidTextEncoding;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function testContentIsSet()
    {
        $text = new Text('foo');
        
        $this->assertEquals('foo', $text->toArray()['content']);
    }
    
    public function testTextCanBeFlashed()
    {
        $text = (new Text('foo'))->flash();

        $this->assertInstanceOf(Text::class, $text);
        $this->assertEquals(true, $text->toArray()['flash']);
    }

    public function testEncodingCanBeSet()
    {
        $text = (new Text('foo'))->encoding('utf-8');

        $this->assertInstanceOf(Text::class, $text);
        $this->assertEquals('utf-8', $text->toArray()['encoding']);
    }

    public function testCantSetInvalidEncoding()
    {
        $this->expectException(InvalidTextEncoding::class);
        
        $text = (new Text('foo'))->encoding('foo');
    }
}
