<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Tests\Integration;

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\MarkdownFile;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Tests\TestCase;
use UserFrosting\UniformResourceLocator\ResourceLocator;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Tests for Markdown/Page/MarkdownFile class.
 */
class MarkdownFileTest extends TestCase
{
    protected $locator;

    public function setUp(): void
    {
        parent::setUp();

        $this->locator = new ResourceLocator(__DIR__);
        $this->locator->registerSharedStream('markdown', '', 'pages');
    }

    public function testPageForFileNotFoundException(): void
    {
        // Set true so it will return a resource even if it doesn't exist
        $resource = $this->locator->getResource('markdown://bar.md', true);
        $this->assertNotFalse($resource);

        $this->expectException(FileNotFoundException::class);
        new MarkdownFile($resource, $this->ci->markdownParser);
    }

    public function testPageForInvalidArgumentExceptionOnExtension(): void
    {
        $resource = $this->locator->getResource('markdown://bar.txt');
        $this->assertNotFalse($resource);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File `markdown://bar.txt` (text/plain) doesn't seems to be a valid markdown file.");
        new MarkdownFile($resource, $this->ci->markdownParser);
    }

    public function testPageForInvalidArgumentExceptionOnMimeType(): void
    {
        $resource = $this->locator->getResource('markdown://image.md');
        $this->assertNotFalse($resource);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File `markdown://image.md` (image/jpeg) doesn't seems to be a valid markdown file.");
        new MarkdownFile($resource, $this->ci->markdownParser);
    }

    public function testConstructor(): MarkdownFile
    {
        $resource = $this->locator->getResource('markdown://test.md');
        $this->assertNotFalse($resource);

        $page = new MarkdownFile($resource, $this->ci->markdownParser);
        $this->assertInstanceOf(PageInterface::class, $page);

        return $page;
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetMetadata(MarkdownFile $page): void
    {
        $this->assertSame([
            'title'       => 'Test page',
            'description' => 'The test page description',
        ], $page->getMetadata());
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetTitle(MarkdownFile $page): void
    {
        $this->assertSame('Test page', $page->getTitle());
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetDescription(MarkdownFile $page): void
    {
        $this->assertSame('The test page description', $page->getDescription());
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetTemplate(MarkdownFile $page): void
    {
        $this->assertSame('test', $page->getTemplate());
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetFilename(MarkdownFile $page): void
    {
        $this->assertSame('test.md', $page->getFilename());
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetPath(MarkdownFile $page): void
    {
        $this->assertSame(__DIR__ . '/pages/test.md', $page->getPath());
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetContent(MarkdownFile $page): void
    {
        $result = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur diam nisl, ullamcorper non lacinia ac, aliquam id leo. Nullam consectetur erat a enim fermentum tempor. Morbi eu malesuada nibh. Donec vel dolor ac urna aliquet pretium eu vel ipsum. Sed eu mattis metus. Pellentesque ex ante, imperdiet non suscipit vel, viverra ac dui. Praesent varius diam ac sapien mattis, eget dapibus felis convallis. Ut placerat est nulla, eget commodo mauris volutpat in.</p>';
        $this->assertSame($result, $page->getContent());
    }

    public function testgetMetadataWithNoMetadata(): void
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $page = new MarkdownFile(__DIR__ . '/pages/test-noMetadata.md', $parser, $filesystem);
        $this->assertSame([], $page->getMetadata());
        $this->assertSame('', $page->getTitle());
        $this->assertSame('', $page->getDescription());
    }

    /*public function testGetSlug(): void
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $page = new MarkdownFile(__DIR__ . '/pages/markdown/01.foo/01.bar/dashboard.md', $parser, $filesystem);

        $this->assertSame('Bar under Foo', $page->getTitle());
        $this->assertSame('dashboard', $page->getTemplate());
        $this->assertSame(__DIR__ . '/pages/markdown/01.foo/01.bar/dashboard.md', $page->getPath());
        $this->assertSame('foo', $page->getSlug());
    }*/
}
