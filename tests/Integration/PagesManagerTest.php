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
use Illuminate\Support\Collection;
use InvalidArgumentException;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PageNode;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\MarkdownFile;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesTree;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\Tests\TestCase;
use UserFrosting\UniformResourceLocator\ResourceInterface;
use UserFrosting\UniformResourceLocator\ResourceLocator;

/**
 *    Tests for MarkdownPages class.
 */
class PagesManagerTest extends TestCase
{
    /** @var ResourceLocator */
    protected $locator;

    public function setUp(): void
    {
        parent::setUp();

        $this->locator = new ResourceLocator(__DIR__);
        $this->locator->registerStream('markdown');
        $this->locator->registerLocation('pages');
    }

    public function testConstructor(): PagesManager
    {
        $parser = new Parsedown();
        $filesystem = new Filesystem();

        $pages = new PagesManager($this->locator, $parser, $this->ci->router, $filesystem);
        $this->assertInstanceOf(PagesManager::class, $pages);
        $this->assertSame($parser, $pages->getParser());

        return $pages;
    }

    public function testServcice(): void
    {
        $this->assertInstanceOf(PagesManager::class, $this->ci->markdownPages);
    }

    /**
     * @depends testConstructor
     */
    public function testGetFiles(PagesManager $pages): void
    {
        $expectedFiles = [
            __DIR__ . '/pages/markdown/01.foo/01.bar/dashboard.md',
            __DIR__ . '/pages/markdown/01.foo/dashboard.md',
            __DIR__ . '/pages/markdown/02.bar/dashboard.md',
            __DIR__ . '/pages/markdown/foobar/dashboard.md',
        ];

        $files = $pages->getFiles();
        $this->assertEquals($expectedFiles, $files);
    }

    /**
     * @depends testConstructor
     */
    /*public function testgetPage(MarkdownPages $pages): void
    {
        $page = $pages->getPage(__DIR__ . '/pages/markdown/02.bar/dashboard.md');
        $this->assertInstanceOf(MarkdownFile::class, $page);

        // Make sure we got the right one, and it's parsed
        $this->assertSame('Bar page', $page->getTitle());
        $this->assertSame('<p>Lorem ipsum <em>dolor</em> sit amet.</p>', $page->getContent());
    }*/

    /**
     * @depends testConstructor
     */
    public function testfindPage(PagesManager $pages): void
    {
        $node = $pages->findPage('foo/bar');

        $this->assertInstanceOf(PageNode::class, $node);
        $this->assertInstanceOf(PageInterface::class, $node->getPage());
        $this->assertSame('foo/bar', $node->getSlug());
    }

    /**
     * @depends testConstructor
     */
    public function testgetPageNode(PagesManager $pages): void
    {
        // Get file
        $file = $this->locator->getResource('markdown://01.foo/01.bar/dashboard.md');
        $this->assertInstanceOf(ResourceInterface::class, $file);

        // Get node
        $node = $pages->getPageNode($file);
        $this->assertInstanceOf(PageNode::class, $node);

        // Assert elements
        $this->assertInstanceOf(PageInterface::class, $node->getPage());
        $this->assertSame('foo/bar', $node->getSlug());
        $this->assertSame('/p/foo/bar/', $node->getUrl()); // Generation is done using the trailing `/`, because the router need to catch both cases

        // Assert parents
        $parent = $node->getParent();
        // $this->assertNotNull($parent);
        $this->assertSame('foo', $parent);
        //$this->assertInstanceOf(PageNode::class, $parent);
    }

    /**
     * @depends testConstructor
     */
    public function testgetTree(PagesManager $pages): void
    {
        $list = $pages->getTree();
        $this->assertInstanceOf(PagesTree::class, $list);

        // Assert countable
        $this->assertCount(4, $list);

        // Assert to array
        $array = $list->toArray();
        $this->assertIsArray($array);
        $this->assertContainsOnlyInstancesOf(PageNode::class, $array);

        $node = $list->forSlug('foo/bar');
        $this->assertInstanceOf(PageNode::class, $node);
        $this->assertSame('foo/bar', $node->getSlug());

        /*$this->assertSame([
            'Bar under Foo',
            'Foo page',
            'Bar page',
            'Foobar page',
        ], array_column($array, 'slug'));*/

        /*$this->assertSame([
            'foo',
            'foo/bar',
            'bar',
            'foobar',
        ], array_column($list, 'slug'));*/
    }

    /**
     *    Test if the manager return the correct thing when given a full path.
     */
    /*public function testMarkdownPagesManager_getPage()
    {
        $page = $this->manager->getPage($this->testPage);
        $this->assertInstanceOf(Page::class, $page);

        // When dealing with a non existing page, an exception should occur
        $this->expectException(FileNotFoundException::class);
        $page = $this->manager->getPage('undefined.md');

        // When dealing with a non markdown file, an exception should occur
        $this->expectException(InvalidArgumentException::class);
        $page = $this->manager->getPage('test.txt');
    }*/
}
