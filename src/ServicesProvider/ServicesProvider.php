<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\ServicesProvider;

use RocketTheme\Toolbox\Event\Event;
use Slim\Container;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\PagesManager;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Sprinkle\MarkdownPages\Twig\MarkdownPagesTwigExtension;

/**
 * Registers services for the MarkdownPages sprinkle.
 */
class ServicesProvider
{
    /**
     * @param Container $container
     */
    public function register(Container $container): void
    {
        $container['markdownPages'] = function ($c) {

            /** @var Parsedown */
            $parser = $c->markdownParser;

            // Create page manager
            $markdown = new PagesManager($c->locator, $parser, $c->router);

            return $markdown;
        };

        $container['markdownParser'] = function ($c) {

            // Create parser
            $parser = new Parsedown();

            // Fire `onMarkdownInitialized` event to register
            $c->eventDispatcher->dispatch('onMarkdownInitialized', new Event(['markdown' => $parser]));

            return $parser;
        };

        /*
         * Extends the 'view' service with the MarkdownPagesTwigExtension for Twig.
         * Adds `markdownPagesTree` functions to Twig.
         */
        $container->extend('view', function ($view, $c) {
            $twig = $view->getEnvironment();
            $extension = new MarkdownPagesTwigExtension($c);
            $twig->addExtension($extension);

            return $view;
        });
    }
}
