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
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PageCollection;
use UserFrosting\Tests\TestCase;

/**
 * Tests for MarkdownPages class.
 */
class PageCollectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testMe(): void
    {
        $collection = new PageCollection();
        $collection->add(Mockery::mock(Page::class)->shouldReceive('getTitle')->andReturn('foo')->getMock())
                    ->add(Mockery::mock(Page::class)->shouldReceive('getTitle')->andReturn('bar')->getMock())
                    ->add(Mockery::mock(Page::class)->shouldReceive('getTitle')->andReturn('foobar')->getMock());

        $this->assertCount(3, $collection);
        $this->assertContainsOnlyInstancesOf(Page::class, $collection);
    }
}
