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
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;
use UserFrosting\Sprinkle\MarkdownPages\Twig\MarkdownPagesTwigExtension;
use UserFrosting\Tests\TestCase;

/**
 * Tests for MarkdownPagesTwigExtension classes
 */
class MarkdownPagesTwigExtensionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testConstructor(): void
    {
        $manager = Mockery::mock(PagesManager::class);
        $manager->shouldReceive('getTree')->with()->once()->andReturn(['foo']);

        $extension = new MarkdownPagesTwigExtension($manager);
        $this->assertInstanceOf(MarkdownPagesTwigExtension::class, $extension);

        $this->assertSame(['markdownPagesTree' => ['foo']], $extension->getGlobals());
    }
}
