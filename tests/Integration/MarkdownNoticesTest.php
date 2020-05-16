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
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Elements\MarkdownNotices;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\MarkdownFile;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Tests\TestCase;

/**
 * Tests for Markdown/Elements/MarkdownNotices class.
 * Also tests the implementation of the custom Parser. If this works, the parser should work too.
 */
class MarkdownNoticesTest extends TestCase
{
    public function testActualResult(): void
    {
        $parser = new Parsedown();
        MarkdownNotices::init($parser);

        // Get the file
        $page = new MarkdownFile(__DIR__ . '/pages/notices.md', $parser, new Filesystem());
        $content = $page->getContent();

        $result = (string) file_get_contents(__DIR__ . '/results/notices.md');
        $this->assertXmlStringEqualsXmlString($result, $content);
    }
}
