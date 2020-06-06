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

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Tests\TestCase;

/**
 * Tests for Services Providers.
 */
class ServicesProviderTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testmarkdownPagesServcice(): void
    {
        $this->assertInstanceOf(PagesManager::class, $this->ci->markdownPages);
    }

    public function testmarkdownParserServcice(): void
    {
        $this->assertInstanceOf(Parsedown::class, $this->ci->markdownParser);
    }

    public function testViewServcice(): void
    {
        $manager = Mockery::mock(PagesManager::class);
        $manager->shouldReceive('getTree')->with()->once()->andReturn([]);
        $this->ci->markdownPages = $manager;

        $this->ci->view->fetch('navigation/markdownPages.html.twig');
    }
}
