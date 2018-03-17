<?php

/*
 * UF MarkdownPages
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2018 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Twig;

use Interop\Container\ContainerInterface;
use Twig_Extension;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\MarkdownPagesManager;

/**
 * MarkdownPagesTwigExtension class.
 * Extends Twig functionality for the MarkdownPages sprinkle.
 */
class MarkdownPagesTwigExtension extends Twig_Extension
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
     * Get the name of this extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'userfrosting/MarkdownPages';
    }

    /**
     * Adds Twig global variables.
     *
     * @return array[mixed]
     */
    public function getGlobals()
    {
        // Create manager instance
        $manager = new MarkdownPagesManager($this->ci);

        return [
            'markdownPagesTree' => $manager->getTree(),
        ];
    }
}
