<?php
/**
 * UF MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2018 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Markdown;

use Pagerange\Markdown\Parsers\YamlParser as MetaParsedown;

/**
 *    Parsedown class.
 *
 *    NOTE: The YamlParser is used directly instead of the
 *    MetaParsedown adapter since we need to access Parsedown
 *    directly to add custom elements
 *
 *    Inspired by grav
 *
 *    @see https://github.com/getgrav/grav/
 */
class Parsedown extends MetaParsedown
{
    public $completable_blocks = [];
    public $continuable_blocks = [];

    /**
     * Be able to define a new Block type or override an existing one.
     *
     * @param $type
     * @param $tag
     */
    public function addBlockType($type, $tag, $continuable = false, $completable = false, $index = null)
    {
        $block = &$this->unmarkedBlockTypes;
        if ($type) {
            if (!isset($this->BlockTypes[$type])) {
                $this->BlockTypes[$type] = [];
            }
            $block = &$this->BlockTypes[$type];
        }

        if (!isset($index)) {
            $block[] = $tag;
        } else {
            array_splice($block, $index, 0, [$tag]);
        }

        if ($continuable) {
            $this->continuable_blocks[] = $tag;
        }
        if ($completable) {
            $this->completable_blocks[] = $tag;
        }
    }

    /**
     * Be able to define a new Inline type or override an existing one.
     *
     * @param $type
     * @param $tag
     */
    public function addInlineType($type, $tag, $index = null)
    {
        if (!isset($index) || !isset($this->InlineTypes[$type])) {
            $this->InlineTypes[$type][] = $tag;
        } else {
            array_splice($this->InlineTypes[$type], $index, 0, [$tag]);
        }

        if (strpos($this->inlineMarkerList, $type) === false) {
            $this->inlineMarkerList .= $type;
        }
    }

    /**
     * Overrides the default behavior to allow for plugin-provided blocks to be continuable.
     *
     * @param $Type
     *
     * @return bool
     */
    protected function isBlockContinuable($Type)
    {
        $continuable = in_array($Type, $this->continuable_blocks) || method_exists($this, 'block'.$Type.'Continue');

        return $continuable;
    }

    /**
     *  Overrides the default behavior to allow for plugin-provided blocks to be completable.
     *
     * @param $Type
     *
     * @return bool
     */
    protected function isBlockCompletable($Type)
    {
        $completable = in_array($Type, $this->completable_blocks) || method_exists($this, 'block'.$Type.'Complete');

        return $completable;
    }

    // For extending this class via plugins
    public function __call($method, $args)
    {
        if (isset($this->$method) === true) {
            $func = $this->$method;

            return call_user_func_array($func, $args);
        }
    }
}
