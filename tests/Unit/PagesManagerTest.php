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
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use UserFrosting\Sprinkle\Core\Router;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\MarkdownFile;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Tests\TestCase;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Tests for MarkdownPages class.
 */
class PagesManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testConstructor(): void
    {
        $locator = Mockery::mock(ResourceLocatorInterface::class);
        $parser = Mockery::mock(Parsedown::class);
        $router = Mockery::mock(Router::class);
        $filesystem = Mockery::mock(Filesystem::class);

        $pages = new PagesManager($locator, $parser, $router, $filesystem);
        $this->assertInstanceOf(PagesManager::class, $pages);
        $this->assertSame($parser, $pages->getParser());
    }

    public function testGetFiles(): void
    {
        $expectedFiles = [
            __DIR__ . '/pages/markdown/foo.md',
            __DIR__ . '/pages/markdown/bar.md',
            __DIR__ . '/pages/markdown/foo/bar.md',
        ];

        $locator = Mockery::mock(ResourceLocatorInterface::class)
            ->shouldReceive('listResources')->with('markdown://')->once()->andReturn($expectedFiles)
            ->getMock();
        $parser = Mockery::mock(Parsedown::class);
        $router = Mockery::mock(Router::class);
        $filesystem = Mockery::mock(Filesystem::class);

        $pages = new PagesManager($locator, $parser, $router, $filesystem);
        $files = $pages->getFiles();

        $this->assertSame($expectedFiles, $files);
    }

    public function testGetPagesWithCustomScheme(): void
    {
        $locator = Mockery::mock(ResourceLocatorInterface::class)
            ->shouldReceive('listResources')->with('foo://')->once()->andReturn([])
            ->getMock();
        $parser = Mockery::mock(Parsedown::class);
        $router = Mockery::mock(Router::class);
        $filesystem = Mockery::mock(Filesystem::class);

        $pages = new PagesManager($locator, $parser, $router, $filesystem);
        $pages->setScheme('foo://');
        $files = $pages->getFiles();

        $this->assertSame([], $files);
    }

    /*public function testgetPage(): void
    {
        $expectedFiles = [
            '01.Bar/docs.md',
            '02.Foo/01.Canada/docs.md',
            '02.Foo/02.France/docs.md',
            '02.Foo/03.Japan/docs.md',
            '02.Foo/04.Mexico/docs.md',
            '02.Foo/04.Mexico/01.Mexican/docs.md',
            '02.Foo/04.Mexico/01.Mexican/01.Bar/docs.md',
            '02.Foo/04.Brazil/docs.md',
            '02.Foo/05.Italy/docs.md',
            '02.Foo/chapter.md',
        ];

        $locator = Mockery::mock(ResourceLocatorInterface::class)
            ->shouldReceive('listResources')->with('markdown://')->once()->andReturn($expectedFiles)
            ->getMock();
        $parser = Mockery::mock(Parsedown::class);
        $filesystem = Mockery::mock(Filesystem::class);

        //$file = Mockery::mock(MarkdownFile::class);

        //$pages = new MarkdownPages($locator, $parser, $filesystem);
        $pages = Mockery::mock(MarkdownPages::class, [$locator, $parser, $filesystem])
            ->makePartial()
            ->shouldReceive('getPage')->andReturn(new FakePageStub())
            ->getMock();

        $list = $pages->getPages();
        $this->assertSame([], $list);

        $this->assertIsArray($list);
        $this->assertCount(10, $list);
        $this->assertEquals([
            'Bar',
            'Foo/Canada',
            'Foo/France',
            'Foo/Japan',
            'Foo/Mexico',
            'Foo/Mexico/Mexican',
            'Foo/Mexico/Mexican/Bar',
            'Foo/Brazil',
            'Foo/Italy',
            'Foo',
        ], $list->pluck('slug')->toArray());

        // Now we'll try to find a page using the previous results
        // $page = $pages->findPage('Foo/Italy');
        // $this->assertInstanceOf(PageInterface::class, $page);
        // $this->assertEquals('02.Foo/05.Italy/docs.md', $page->relativePath);
        // $this->assertEquals('Foo/Italy', $page->slug);

        // We can now test the treeview
        // $tree = $pages->getTree();
        // $this->assertInstanceOf(Collection::class, $tree);
        // $this->assertCount(2, $tree);
        // $this->assertEquals('Foo/Mexico/Mexican/Bar', $tree['Foo']->children['Mexico']->children['Mexican']->children['Bar']->slug);

        // And one with a top level slug
        // $tree = $pages->getTree('Foo/Mexico');
        // $this->assertInstanceOf(Collection::class, $tree);
        // $this->assertCount(1, $tree);
        // $this->assertEquals('Foo/Mexico/Mexican/Bar', $tree['Mexican']->children['Bar']->slug);
    }*/
}

class FakePageStub implements PageInterface
{
    public function getMetadata(): array
    {
        return [];
    }

    public function addMetadata(string $slug, $value)
    {
        return $this;
    }

    public function getTitle(): string
    {
        return '';
    }

    public function getDescription(): string
    {
        return '';
    }

    public function getTemplate(): string
    {
        return '';
    }

    public function getContent(): string
    {
        return '';
    }
}