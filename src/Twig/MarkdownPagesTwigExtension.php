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

use Psr\Container\ContainerInterface;
use Twig\Extension\AbstractExtension;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;

/**
 * MarkdownPagesTwigExtension class.
 * Extends Twig functionality for the MarkdownPages sprinkle.
 */
class MarkdownPagesTwigExtension extends AbstractExtension
{
    /**
     * @var ContainerInterface The global container object, which holds all your services.
     */
    protected $ci;

    /**
     * Constructor.
     *
     * @param ContainerInterface $ci The global container object, which holds all your services.
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * Adds Twig global variables.
     *
     * @return mixed[]
     */
    public function getGlobals()
    {
        // Create manager instance
        $manager = new PagesManager($this->ci);

        return [
            'markdownPagesTree' => $manager->getTree(),
        ];
    }
}
