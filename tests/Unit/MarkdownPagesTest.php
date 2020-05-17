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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\MarkdownPages;
use UserFrosting\Tests\TestCase;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Tests for MarkdownPages class.
 */
class MarkdownPagesTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testConstructor(): void
    {
        $locator = Mockery::mock(ResourceLocatorInterface::class);

        $pages = new MarkdownPages($locator);
        $this->assertInstanceOf(MarkdownPages::class, $pages);
    }

    public function testGetPages(): void
    {
        $expectedFiles = [
            __DIR__ . '/pages/markdown/foo.md',
            __DIR__ . '/pages/markdown/bar.md',
            __DIR__ . '/pages/markdown/foo/bar.md',
        ];

        $locator = Mockery::mock(ResourceLocatorInterface::class)
            ->shouldReceive('listResources')->with('markdown://')->once()->andReturn($expectedFiles)
            ->getMock();

        $pages = new MarkdownPages($locator);
        $files = $pages->getFiles();

        $this->assertSame($expectedFiles, $files);
    }

    public function testGetPagesWithCustomScheme(): void
    {
        $locator = Mockery::mock(ResourceLocatorInterface::class)
            ->shouldReceive('listResources')->with('foo://')->once()->andReturn([])
            ->getMock();

        $pages = new MarkdownPages($locator);
        $pages->setScheme('foo://');
        $files = $pages->getFiles();
    }
}
