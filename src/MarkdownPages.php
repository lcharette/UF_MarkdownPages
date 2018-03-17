<?php

/*
 * UF MarkdownPages
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2018 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages;

use RocketTheme\Toolbox\Event\Event;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Elements\MarkdownNotices;
use UserFrosting\System\Sprinkle\Sprinkle;

/**
 *    Bootstrapper class for the MarkdownPages sprinkle.
 */
class MarkdownPages extends Sprinkle
{
    /**
     *    Defines which events in the UF lifecycle our Sprinkle should hook into.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0],
        ];
    }

    /**
     *    Listener for onMarkdownInitialized events
     *    Adds custom markdown elements.
     *
     *    @param  Event $event
     *
     *    @return void
     */
    public function onMarkdownInitialized(Event $event)
    {
        $markdownNotices = new MarkdownNotices();
        $markdownNotices->onMarkdownInitialized($event);
    }
}
