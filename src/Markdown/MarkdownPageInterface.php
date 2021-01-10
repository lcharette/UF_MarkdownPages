<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Markdown;

/**
 *   MarkdownPageInterface.
 */
interface MarkdownPageInterface
{
    public function getMetadata();

    public function getTitle();

    public function getDescription();

    public function getFilename();

    public function getTemplate();

    public function getPath();

    public function getContent();
}
