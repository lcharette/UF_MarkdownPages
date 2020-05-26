<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\MarkdownFile;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Tests\TestCase;

/**
 * Tests for Markdown/Page/MarkdownFile classes
 */
class MarkdownFileTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testPageForFileNotFoundException(): void
    {
        $parser = Mockery::mock(Parsedown::class);
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->with('foo/bar.md')->once()->andReturn(false)
            ->getMock();

        $this->expectException(FileNotFoundException::class);
        $page = new MarkdownFile('foo/bar.md', $parser, $filesystem);
    }

    public function testPageForInvalidArgumentExceptionOnExtension(): void
    {
        $parser = Mockery::mock(Parsedown::class);
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->with('foo/bar.txt')->once()->andReturn(true)
            ->shouldReceive('extension')->with('foo/bar.txt')->once()->andReturn('txt')
            ->shouldReceive('mimeType')->with('foo/bar.txt')->once()->andReturn('text/plain')
            ->shouldReceive('basename')->with('foo/bar.txt')->once()->andReturn('bar.txt')
            ->getMock();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File `bar.txt` (text/plain) doesn't seems to be a valid markdown file.");
        $page = new MarkdownFile('foo/bar.txt', $parser, $filesystem);
    }

    public function testPageForInvalidArgumentExceptionOnMimeType(): void
    {
        $parser = Mockery::mock(Parsedown::class);
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->with('foo/bar.md')->once()->andReturn(true)
            ->shouldReceive('extension')->with('foo/bar.md')->once()->andReturn('md')
            ->shouldReceive('mimeType')->with('foo/bar.md')->twice()->andReturn('image/jpeg')
            ->shouldReceive('basename')->with('foo/bar.md')->once()->andReturn('bar.md')
            ->getMock();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File `bar.md` (image/jpeg) doesn't seems to be a valid markdown file.");
        $page = new MarkdownFile('foo/bar.md', $parser, $filesystem);
    }

    public function testConstructor(): void
    {
        $parser = Mockery::mock(Parsedown::class);
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->with('foo/bar.md')->once()->andReturn(true)
            ->shouldReceive('extension')->with('foo/bar.md')->once()->andReturn('md')
            ->shouldReceive('mimeType')->with('foo/bar.md')->once()->andReturn('text/plain')
            ->shouldReceive('get')->with('foo/bar.md')->once()
            ->getMock();

        $page = new MarkdownFile('foo/bar.md', $parser, $filesystem);
        $this->assertInstanceOf(PageInterface::class, $page);
    }

    /**
     * Also check the no-title / no-descripbtion behaviour.
     * Make sure the local cache work with meta being called only once.
     *
     * @depends testConstructor
     */
    public function testgetMetadata(): void
    {
        $parser = Mockery::mock(Parsedown::class)
            ->shouldReceive('meta')->with('foo')->once()->andReturn(['bar' => true])
            ->getMock();
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->andReturn(true)
            ->shouldReceive('extension')->andReturn('md')
            ->shouldReceive('mimeType')->andReturn('text/plain')
            ->shouldReceive('get')->andReturn('foo')
            ->getMock();

        $page = new MarkdownFile('foo/bar.md', $parser, $filesystem);
        $this->assertSame(['bar' => true], $page->getMetadata());
        $this->assertSame('', $page->getTitle());
        $this->assertSame('', $page->getDescription());
    }

    /**
     * @depends testgetMetadata
     */
    public function testgetTitleAndDescription(): void
    {
        $parser = Mockery::mock(Parsedown::class)
            ->shouldReceive('meta')->with('foo')->once()->andReturn([
                'title'       => 'foo',
                'description' => 'bar',
                'foo'         => 'foobar',
            ])
            ->getMock();
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->andReturn(true)
            ->shouldReceive('extension')->andReturn('md')
            ->shouldReceive('mimeType')->andReturn('text/plain')
            ->shouldReceive('get')->andReturn('foo')
            ->getMock();

        $page = new MarkdownFile('foo/bar.md', $parser, $filesystem);
        $this->assertSame('foo', $page->getTitle());
        //$this->assertSame('foo', $page->title);
        //$this->assertSame('bar', $page->description);

        // Test addMetadata
        $page->addMetadata('foo', 'bar');
        $page->addMetadata('title', 'foobar');
        $this->assertSame([
            'title' => 'foobar',
            'description' => 'bar',
            'foo' => 'bar'
        ], $page->getMetadata());
        $this->assertSame('bar', $page->getMetadata()['foo']);
        //$this->assertSame('bar', $page->foo);

        // Not defined metadata
        /*$this->assertFalse(isset($page->bar));
        $this->expectException(\Exception::class);
        $bar = $page->bar;*/
    }

    public function testGetSlug(): void
    {
        // getSlug
    }

    /**
     * @depends testConstructor
     */
    public function testgetTemplateAndgetFilenameAndgetPath(): void
    {
        $parser = Mockery::mock(Parsedown::class);
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->andReturn(true)
            ->shouldReceive('extension')->andReturn('md')
            ->shouldReceive('mimeType')->andReturn('text/plain')
            ->shouldReceive('get')->andReturn('foo')
            ->getMock();

        $page = new MarkdownFile('foo/bar.md', $parser, $filesystem);
        $this->assertSame('bar', $page->getTemplate());
        $this->assertSame('bar.md', $page->getFilename());
        $this->assertSame('foo/bar.md', $page->getPath());
    }

    /**
     * Also check the no-title / no-descripbtion behaviour.
     * Make sure the local cache work with meta being called only once.
     *
     * @depends testConstructor
     */
    public function testgetContent(): void
    {
        $parser = Mockery::mock(Parsedown::class)
            ->shouldReceive('text')->with('_foo_')->once()->andReturn('<i>Foo</i>')
            ->getMock();
        $filesystem = Mockery::mock(Filesystem::class)
            ->shouldReceive('exists')->andReturn(true)
            ->shouldReceive('extension')->andReturn('md')
            ->shouldReceive('mimeType')->andReturn('text/plain')
            ->shouldReceive('get')->andReturn('_foo_')
            ->getMock();

        $page = new MarkdownFile('foo/bar.md', $parser, $filesystem);
        $this->assertSame('<i>Foo</i>', $page->getContent());
    }
}
