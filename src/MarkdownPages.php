<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages;

use RocketTheme\Toolbox\Event\Event;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Elements\MarkdownNotices;
use UserFrosting\System\Sprinkle\Sprinkle;

/**
 * Bootstrapper class for the MarkdownPages sprinkle.
 */
class MarkdownPages extends Sprinkle
{
    /**
     * @return mixed[] List of events to subscribe, formatted as ['eventName' => ['methodName', $priority]].
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onAppInitialize'       => ['onAppInitialize', 0],
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0],
        ];
    }

    /**
     * Set static references to DI container in necessary classes.
     */
    public function onAppInitialize(): void
    {
        /** @var \UserFrosting\UniformResourceLocator\ResourceLocator $locator */
        $locator = $this->ci->locator;

        $locator->registerStream('markdown', '', 'pages');
    }

    /**
     * Listener for onMarkdownInitialized events.
     * Adds custom markdown elements.
     *
     * @param Event $event
     */
    public function onMarkdownInitialized(Event $event): void
    {
        MarkdownNotices::init($event['markdown']);
    }
}
