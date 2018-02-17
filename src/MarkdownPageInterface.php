<?php
/**
*    UF MarkdownPages
*
*    @author Louis Charette
*    @copyright Copyright (c) 2018 Louis Charette
*    @link      https://github.com/lcharette/UF_MarkdownPages
*    @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
*/
namespace UserFrosting\Sprinkle\MarkdownPages;

/**
 *   MarkdownPageInterface
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