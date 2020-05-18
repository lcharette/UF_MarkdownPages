<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Markdown\Page;

/**
 * Page Interface.
 *
 * A Page can be created from different sources (file, db, etc.) and in different language (Markdown, Txt, html, etc.).
 * A page has metadata, a title, a description, a template and some content.
 */
interface PageInterface
{
    /**
     * Returns the file metadata.
     *
     * @return string[]
     */
    public function getMetadata(): array;

    /**
     * Add a custom metadata.
     *
     * @param string $slug
     * @param mixed  $value
     *
     * @return self
     */
    public function addMetadata(string $slug, $value);

    /**
     * Returns the file title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns the file description.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Return the page template, aka the filename without the extension.
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * Returns the file parsed content as HTML.
     *
     * @return string
     */
    public function getContent(): string;
}
