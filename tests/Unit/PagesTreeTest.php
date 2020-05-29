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
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PageNode;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesTree;
use UserFrosting\Tests\TestCase;

/**
 * Tests for MarkdownPages class.
 */
class PagesTreeTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testMe(): void
    {
        $collection = new PagesTree();
        $collection->add(Mockery::mock(PageNode::class)->shouldReceive('getTitle')->andReturn('foo')->getMock())
                    ->add(Mockery::mock(PageNode::class)->shouldReceive('getTitle')->andReturn('bar')->getMock())
                    ->add(Mockery::mock(PageNode::class)->shouldReceive('getTitle')->andReturn('foobar')->getMock());

        $this->assertCount(3, $collection);
        $this->assertContainsOnlyInstancesOf(PageNode::class, $collection);
    }
}
