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

use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Tests\TestCase;

/**
 * Tests for MarkdownPages class.
 */
class ServicesProviderTest extends TestCase
{
    public function testmarkdownPagesServcice(): void
    {
        $this->assertInstanceOf(PagesManager::class, $this->ci->markdownPages);
    }

    public function testmarkdownParserServcice(): void
    {
        $this->assertInstanceOf(Parsedown::class, $this->ci->markdownParser);
    }

    /*public function testViewServcice(): void
    {
        $this->ci->markdownPages;

        //$this->assertInstanceOf(PagesManager::class, $this->ci->markdownPages);
    }*/
}
