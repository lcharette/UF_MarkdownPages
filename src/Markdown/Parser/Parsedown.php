<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser;

use Pagerange\Markdown\Parsers\YamlParser as MetaParsedown;

/**
 * Parsedown class.
 *
 * NOTE: The YamlParser is used directly instead of the
 * MetaParsedown adapter since we need to access Parsedown
 * directly to add custom elements
 *
 * Inspired by grav
 *
 * @see https://github.com/getgrav/grav/
 */
class Parsedown extends MetaParsedown
{
    /** @var array<string,callable> */
    protected $custom_blocks = [];

    /** @var array<string,callable> */
    protected $completable_blocks = [];

    /** @var array<string,callable> */
    protected $continuable_blocks = [];

    /**
     * Be able to define a new Block type or override an existing one.
     *
     * @param string|false $type
     * @param string       $tag
     * @param int|null     $index
     */
    public function addBlockType($type, $tag, $index = null): void
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
    }

    public function registerBlockMethod(string $type, callable $callback): void
    {
        $this->custom_blocks['block' . $type] = $callback;
    }

    public function registerContinuableBlockMethod(string $type, callable $callback): void
    {
        $this->continuable_blocks['block' . $type . 'Continue'] = $callback;
    }

    public function registerCompletableBlockMethod(string $type, callable $callback): void
    {
        $this->completable_blocks['block' . $type . 'Complete'] = $callback;
    }

    /**
     * Be able to define a new Inline type or override an existing one.
     *
     * @param string   $type
     * @param string   $tag
     * @param int|null $index
     */
    public function addInlineType($type, $tag, $index = null): void
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
     * @param string $type
     *
     * @return bool
     */
    protected function isBlockContinuable($type)
    {
        $method = 'block' . $type . 'Continue';
        $continuable = array_key_exists($method, $this->continuable_blocks) || method_exists($this, $method);

        return $continuable;
    }

    /**
     *  Overrides the default behavior to allow for plugin-provided blocks to be completable.
     *
     * @param string $type
     *
     * @return bool
     */
    protected function isBlockCompletable($type)
    {
        $method = 'block' . $type . 'Complete';
        $completable = array_key_exists($method, $this->completable_blocks) || method_exists($this, $method);

        return $completable;
    }

    /**
     * For extending this class via plugins
     *
     * @param string  $method
     * @param mixed[] $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if (isset($this->custom_blocks[$method]) === true) {
            $func = $this->custom_blocks[$method];

            return call_user_func_array($func, $args);
        }

        if (isset($this->completable_blocks[$method]) === true) {
            $func = $this->completable_blocks[$method];

            return call_user_func_array($func, $args);
        }

        if (isset($this->continuable_blocks[$method]) === true) {
            $func = $this->continuable_blocks[$method];

            return call_user_func_array($func, $args);
        }
    }
}
