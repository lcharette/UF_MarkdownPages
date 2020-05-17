<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Markdown\Elements;

use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Sprinkle\Core\Facades\Translator;

/**
 * MarkdownNotices class.
 * Adds the `notice` markdown Multi-Line Element
 * Based on Grav Markdown Notices Plugin.
 *
 * @see https://github.com/getgrav/grav-plugin-markdown-notices
 */
class MarkdownNotices
{
    /**
     * @var string[] The different level names. Also acts as css selector
     */
    protected static $level_classes = ['yellow', 'red', 'blue', 'green'];

    /**
     * Register onMarkdownInitialized event.
     *
     * @param Parsedown $markdown
     */
    public static function init(Parsedown $markdown): void
    {
        $markdown->addBlockType('!', 'Notices');
        $markdown->registerBlockMethod('Notices', [__CLASS__, 'blockMethod']);
        $markdown->registerContinuableBlockMethod('Notices', [__CLASS__, 'continuableBlockMethod']);
    }

    /**
     * Main block method.
     *
     * @param mixed[] $line
     *
     * @return mixed[]|null
     */
    public static function blockMethod($line): ?array
    {
        if (preg_match('/^(!{1,' . count(self::$level_classes) . '})[ ]+(.*)/', $line['text'], $matches)) {
            $level = strlen($matches[1]) - 1;

            $text = $matches[2];

            return [
                'element' => [
                    'name'       => 'div',
                    'handler'    => 'lines',
                    'attributes' => [
                        'class'      => 'notices ' . self::$level_classes[$level],
                        'data-label' => self::getNoticeLabel($level),
                    ],
                    'text' => (array) $text,
                ],
            ];
        }

        return null;
    }

    /**
     * Continuable Block Method.
     *
     * @param mixed[] $line
     * @param mixed[] $block
     *
     * @return mixed[]
     */
    public static function continuableBlockMethod(array $line, array $block): ?array
    {
        if (isset($block['interrupted'])) {
            return null;
        }

        if ($line['text'][0] === '!' and preg_match('/^(!{1,' . count(self::$level_classes) . '})(.*)/', $line['text'], $matches)) {
            $block['element']['text'][] = ltrim($matches[2]);

            return $block;
        }

        return null;
    }

    /**
     * Return the localized label for a notice.
     *
     * @param int $level The notice level
     *
     * @return string The localized label
     */
    protected static function getNoticeLabel($level)
    {
        $class = self::$level_classes[$level];

        return Translator::translate('MARKDOWNPAGES.NOTICES.' . strtoupper($class));
    }
}
