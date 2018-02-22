# Extending Parsedown

Markdown pages uses [Parsedown](https://github.com/erusev/parsedown) and [MetaParsedown](https://github.com/pagerange/metaparsedown) as the markdown parser. You can use [Parsedown extension feature](https://github.com/erusev/parsedown/wiki/Tutorial:-Create-Extensions) to add your own elements.

## Listening to the `onMarkdownInitialized` event

In your sprinkle [Bootstrapper class](https://learn.userfrosting.com/advanced/application-lifecycle#bootstrapper-classes), you'll need to listen for the `onMarkdownInitialized` event. This event is fired once the base markdown parser is setup and allows you to add your custom elements to the parser.

For example :

```
namespace UserFrosting\Sprinkle\Site;

use RocketTheme\Toolbox\Event\Event;
use UserFrosting\Sprinkle\Site\SomeRandomStaticClass;
use UserFrosting\System\Sprinkle\Sprinkle;

class Site extends Sprinkle
{
    /**
     * Defines which events in the UF lifecycle our Sprinkle should hook into.
     */
    public static function getSubscribedEvents()
    {
        return [
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0]
        ];
    }

    /**
     * Adds custom markdown elements
     */
    public function onMarkdownInitialized(Event $event)
    {
        $markdown = $event['markdown'];

        $markdown->addBlockType('!', 'Notices', true, false);

        $markdown->blockNotices = function($Line) {
            // ...
        };
        $markdown->blockNoticesContinue = function($Line, array $Block) {
            // ...
        };
    }
}
```

The `$markdown` variable will contain the markdown parser and `addBlockType` or `addInlineType` methods can be used to register new block or inline elements. At this point, refer to [Parsedown documentation](https://github.com/erusev/parsedown/wiki/Tutorial:-Create-Extensions) on how to setup your own markdown element. Only difference is element functions should be added as variable instead of declared as functions.
