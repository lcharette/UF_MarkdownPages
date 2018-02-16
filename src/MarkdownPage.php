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

use Illuminate\Cache\Repository as Cache;
use UserFrosting\Sprinkle\MarkdownPages\MarkdownPageInterface;

/**
 *   MarkdownPage
 */
class MarkdownPage implements MarkdownPageInterface
{
    /**
     * @var Cache $cache
     */
    protected $cache;

    /**
     * @var string $path The file path
     */
    protected $path;

    /**
     * @var array $path The file metadata
     */
    protected $metadata;

    /**
     * @var string $path The file path
     */
    protected $content;

    public function __construct(Cache $cache, string $path)
    {

    }

    protected function read()
    {
        // return string
    }

    public function getMetadata()
    {
        //return array
    }

    public function getTitle()
    {
        //return string
    }

    public function getSlug()
    {
        //return string
    }

    public function getDescription()
    {
        //return string
    }

    public function getPath()
    {
        //return string
    }

    public function getUrl()
    {
        //return string
    }

    public function getContent()
    {
        //return string
    }
}