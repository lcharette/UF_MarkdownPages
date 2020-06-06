<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;

/**
 * MarkdownPagesTwigExtension class.
 * Extends Twig functionality for the MarkdownPages sprinkle.
 */
class MarkdownPagesTwigExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var PagesManager
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param PagesManager $manager
     */
    public function __construct(PagesManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Adds Twig global variables.
     *
     * @return mixed[]
     */
    public function getGlobals()
    {
        $tree = $this->manager->getTree();

        return [
            'markdownPagesTree' => $tree,
        ];
    }
}
