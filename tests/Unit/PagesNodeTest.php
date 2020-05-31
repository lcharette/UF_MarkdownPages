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
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PageNode;
use UserFrosting\Tests\TestCase;

/**
 * Tests for PageNode class.
 */
class PagesNodeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testMe(): void
    {
        $page = Mockery::mock(PageInterface::class);
        $slug = 'foo';

        $node = new PageNode($page, $slug);
        $this->assertInstanceOf(PageNode::class, $node);
        $this->assertSame($page, $node->getPage());
        $this->assertSame($slug, $node->getSlug());
        $this->assertNull($node->getParent());

        // $parentNode = new PageNode($page, 'bar');
        // $node->setParent($parentNode);
        // $this->assertSame($parentNode, $node->getParent());
        // $this->assertSame('bar', $node->getParent()->getSlug());

        // $node->setParent(null);
        // $this->assertNull($node->getParent());
    }
}
