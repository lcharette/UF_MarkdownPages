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

/**
 * Tests for Markdown/Page/MarkdownFile class.
 */
class MarkdownFileTest extends TestCase
{
    public function testPageForFileNotFoundException(): void
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $this->expectException(FileNotFoundException::class);
        $page = new MarkdownFile(__DIR__ . '/pages/bar.md', $parser, $filesystem, null);
    }

    public function testPageForInvalidArgumentExceptionOnExtension(): void
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File `bar.txt` (text/plain) doesn't seems to be a valid markdown file.");
        $page = new MarkdownFile(__DIR__ . '/pages/bar.txt', $parser, $filesystem, null);
    }

    public function testPageForInvalidArgumentExceptionOnMimeType(): void
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("File `image.md` (image/jpeg) doesn't seems to be a valid markdown file.");
        $page = new MarkdownFile(__DIR__ . '/pages/image.md', $parser, $filesystem, null);
    }

    public function testConstructor(): MarkdownFile
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $page = new MarkdownFile(__DIR__ . '/pages/test.md', $parser, $filesystem, null);
        $this->assertInstanceOf(PageInterface::class, $page);

        return $page;
    }

    /**
     * @depends testConstructor
     */
    public function testgetMetadata(MarkdownFile $page): void
    {
        $this->assertSame([
            'title'       => 'Test page',
            'description' => 'The test page description',
        ], $page->getMetadata());
    }

    /**
     * @depends testConstructor
     */
    public function testgetTitle(MarkdownFile $page): void
    {
        $this->assertSame('Test page', $page->getTitle());
    }

    /**
     * @depends testConstructor
     */
    public function testgetDescription(MarkdownFile $page): void
    {
        $this->assertSame('The test page description', $page->getDescription());
    }

    /**
     * @depends testConstructor
     */
    public function testgetTemplate(MarkdownFile $page): void
    {
        $this->assertSame('test', $page->getTemplate());
    }

    /**
     * @depends testConstructor
     */
    public function testgetFilename(MarkdownFile $page): void
    {
        $this->assertSame('test.md', $page->getFilename());
    }

    /**
     * @depends testConstructor
     */
    public function testgetPath(MarkdownFile $page): void
    {
        $this->assertSame(__DIR__ . '/pages/test.md', $page->getPath());
    }

    /**
     * @depends testConstructor
     */
    public function testgetContent(MarkdownFile $page): void
    {
        $result = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur diam nisl, ullamcorper non lacinia ac, aliquam id leo. Nullam consectetur erat a enim fermentum tempor. Morbi eu malesuada nibh. Donec vel dolor ac urna aliquet pretium eu vel ipsum. Sed eu mattis metus. Pellentesque ex ante, imperdiet non suscipit vel, viverra ac dui. Praesent varius diam ac sapien mattis, eget dapibus felis convallis. Ut placerat est nulla, eget commodo mauris volutpat in.</p>';
        $this->assertSame($result, $page->getContent());
    }

    public function testgetMetadataWithNoMetadata(): void
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $page = new MarkdownFile(__DIR__ . '/pages/test-noMetadata.md', $parser, $filesystem, null);
        $this->assertSame([], $page->getMetadata());
        $this->assertSame('', $page->getTitle());
        $this->assertSame('', $page->getDescription());
    }
}
