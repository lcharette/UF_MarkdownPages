<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Markdown;

use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;

class PageNode
{
    /** @var PageInterface */
    protected $page;

    /** @var string */
    protected $slug;

    /** @var PageNode */
    protected $parent;

    public function __construct(PageInterface $page, string $slug)
    {
        $this->page = $page;
        $this->slug = $slug;
    }

    /**
     * Get the value of page
     */
    public function getPage(): PageInterface
    {
        return $this->page;
    }

    /**
     * Get the value of slug
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * Get the value of parent
     */
    public function getParent(): PageNode
    {
        return $this->parent;
    }

    /**
     * Set the value of parent
     *
     * @return self
     */
    public function setParent(PageNode $parent)
    {
        $this->parent = $parent;

        return $this;
    }
}
