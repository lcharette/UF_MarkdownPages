<?php

/*
 * UF MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2018 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\ServicesProvider;

use Interop\Container\ContainerInterface;
use RocketTheme\Toolbox\Event\Event;
use RocketTheme\Toolbox\Event\EventDispatcher;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parsedown;
use UserFrosting\Sprinkle\MarkdownPages\Twig\MarkdownPagesTwigExtension;

/**
 *    ServicesProvider class.
 *    Registers services for the MarkdownPages sprinkle.
 */
class ServicesProvider
{
    /**
     *    @param ContainerInterface $container
     */
    public function register(ContainerInterface $container)
    {
        /*
         *    Creates the markdown parser service
         */
        $container['markdown'] = function ($c) {

            /** @var EventDispatcher $events */
            $eventDispatcher = $c->eventDispatcher;

            // Get instance
            $markdown = new Parsedown();

            // Fire `onMarkdownInitialized` event
            $eventDispatcher->dispatch('onMarkdownInitialized', new Event(['markdown' => $markdown]));

            return $markdown;
        };

        /*
         *    Extends the 'locator' service with custom streams
         *    Custom stream added: pages://
         */
        // NB: This requires UserFrosting issue #853 to be fixed.
        // This service can't be extend as of now
        // @see: https://github.com/userfrosting/UserFrosting/issues/853
        /*$container->extend('streamBuilder', function ($app) {
            //$locator->addPath('pages', '', \UserFrosting\APP_DIR_NAME . '/pages');
            return $app['streamBuilder'];
        });*/

        /*
         *    Extends the 'view' service with the MarkdownPagesTwigExtension for Twig.
         *    Adds `markdownPagesTree` functions to Twig.
         */
        $container->extend('view', function ($view, $c) {
            $twig = $view->getEnvironment();
            $extension = new MarkdownPagesTwigExtension($c);
            $twig->addExtension($extension);

            return $view;
        });
    }
}
