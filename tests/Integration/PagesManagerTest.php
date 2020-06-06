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
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Tests\TestCase;
use UserFrosting\UniformResourceLocator\ResourceInterface;
use UserFrosting\UniformResourceLocator\ResourceLocator;

/**
 * Tests for MarkdownPages class.
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
        $expected = [
            // Slug         => Path
            'foo/bar'       => __DIR__ . '/pages/markdown/01.foo/02.bar/dashboard.md',
            'foo/foo'       => __DIR__ . '/pages/markdown/01.foo/01.foo/dashboard.md',
            'foo/bar/foo'   => __DIR__ . '/pages/markdown/01.foo/02.bar/foo/dashboard.md',
            'foo'           => __DIR__ . '/pages/markdown/01.foo/dashboard.md',
            'bar'           => __DIR__ . '/pages/markdown/02.bar/dashboard.md',
            'foobar'        => __DIR__ . '/pages/markdown/foobar/dashboard.md',
        ];

        $result = $pages->getFiles();
        $this->assertEquals($expected, $result);
    }

    /**
     * @depends testConstructor
     */
    public function testfindFile(PagesManager $pages): void
    {
        $file = $pages->findFile('foo/bar');

        $this->assertInstanceOf(ResourceInterface::class, $file);
        $this->assertEquals(__DIR__ . '/pages/markdown/01.foo/02.bar/dashboard.md', $file);
    }

    /**
     * @depends testConstructor
     */
    public function testfindPage(PagesManager $pages): void
    {
        $page = $pages->findPage('foo/bar/foo');

        $this->assertInstanceOf(PageInterface::class, $page);
        $this->assertSame('Foo under Bar under Foo', $page->getTitle());
    }

    /**
     * @depends testConstructor
     */
    public function testGetNodes(PagesManager $pages): void
    {
        $expected = [
            'foo/foo' => [
                'slug'     => 'foo/foo',
                'title'    => 'Foo under Foo',
                'url'      => '/p/foo/foo/',
                'parent'   => 'foo',
                'metadata' => [
                    'title'       => 'Foo under Foo',
                    'description' => 'The bar page description',
                ],
                'children' => [],
            ],
            'foo/bar' => [
                'slug'     => 'foo/bar',
                'title'    => 'Bar under Foo',
                'url'      => '/p/foo/bar/',
                'parent'   => 'foo',
                'metadata' => [
                    'title'       => 'Bar under Foo',
                    'description' => 'The bar page description',
                ],
                'children' => [],
            ],
            'foo/bar/foo' => [
                'slug'     => 'foo/bar/foo',
                'title'    => 'Foo under Bar under Foo',
                'url'      => '/p/foo/bar/foo/',
                'parent'   => 'foo/bar',
                'metadata' => [
                    'title'       => 'Foo under Bar under Foo',
                    'description' => 'The bar page description',
                ],
                'children' => [],
            ],
            'foo' => [
                'slug'     => 'foo',
                'title'    => 'Foo page',
                'url'      => '/p/foo/',
                'parent'   => '',
                'metadata' => [
                    'title'       => 'Foo page',
                    'description' => 'The foo page description',
                ],
                'children' => [],
            ],
            'bar' => [
                'slug'     => 'bar',
                'title'    => 'Bar page',
                'url'      => '/p/bar/',
                'parent'   => '',
                'metadata' => [
                    'title'       => 'Bar page',
                    'description' => 'The bar page description',
                ],
                'children' => [],
            ],
            'foobar' => [
                'slug'     => 'foobar',
                'title'    => 'Foobar page',
                'url'      => '/p/foobar/',
                'parent'   => '',
                'metadata' => [
                    'title'       => 'Foobar page',
                    'description' => 'The foo/bar page description',
                ],
                'children' => [],
            ],
        ];

        $result = $pages->getNodes();

        $this->assertSame($expected, $result);
    }

    /**
     * @depends testConstructor
     */
    public function testTemplateTree(PagesManager $pages): void
    {
        $expected = [
            'foo' => [
                'slug'     => 'foo',
                'title'    => 'Foo page',
                'url'      => '/p/foo/',
                'parent'   => '',
                'metadata' => [
                    'title'       => 'Foo page',
                    'description' => 'The foo page description',
                ],
                'children' => [
                    'foo/foo' => [
                        'slug'     => 'foo/foo',
                        'title'    => 'Foo under Foo',
                        'url'      => '/p/foo/foo/',
                        'parent'   => 'foo',
                        'metadata' => [
                            'title'       => 'Foo under Foo',
                            'description' => 'The bar page description',
                        ],
                        'children' => [],
                    ],
                    'foo/bar' => [
                        'slug'     => 'foo/bar',
                        'title'    => 'Bar under Foo',
                        'url'      => '/p/foo/bar/',
                        'parent'   => 'foo',
                        'metadata' => [
                            'title'       => 'Bar under Foo',
                            'description' => 'The bar page description',
                        ],
                        'children' => [
                            'foo/bar/foo' => [
                                'slug'     => 'foo/bar/foo',
                                'title'    => 'Foo under Bar under Foo',
                                'url'      => '/p/foo/bar/foo/',
                                'parent'   => 'foo/bar',
                                'metadata' => [
                                    'title'       => 'Foo under Bar under Foo',
                                    'description' => 'The bar page description',
                                ],
                                'children' => [],
                            ],
                        ],
                    ],
                ],
            ],
            'bar' => [
                'slug'     => 'bar',
                'title'    => 'Bar page',
                'url'      => '/p/bar/',
                'parent'   => '',
                'metadata' => [
                    'title'       => 'Bar page',
                    'description' => 'The bar page description',
                ],
                'children' => [],
            ],
            'foobar' => [
                'slug'     => 'foobar',
                'title'    => 'Foobar page',
                'url'      => '/p/foobar/',
                'parent'   => '',
                'metadata' => [
                    'title'       => 'Foobar page',
                    'description' => 'The foo/bar page description',
                ],
                'children' => [],
            ],
        ];

        $tree = $pages->getTree();
        $this->assertEquals($expected, $tree);
    }
}
