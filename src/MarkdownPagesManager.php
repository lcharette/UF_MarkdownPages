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
use RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator;

/**
 *   MarkdownPagesManager
 */
class MarkdownPagesManager
{
    /**
     * @var UniformResourceLocator $locator
     */
    protected $locator;

    /**
     * @var Cache $cache
     */
    protected $cache;

    /**
     *    Constructor
     *
     *    @param UniformResourceLocator $locator
     *    @param Cache                  $cache
     */
    public function __construct(UniformResourceLocator $locator, Cache $cache)
    {
        $this->locator = $locator;
        $this->cache = $cache;
    }

    public function getAllFiles()
    {
        // Return [MarkdownPage]
    }

    public function getFile($path)
    {
        // return MarkdownPage
    }

    public function getTree()
    {
        //return array - nested list of files. Needs metadata of each files
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param Cache $cache
     *
     * @return static
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * @return UniformResourceLocator
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * @param UniformResourceLocator $locator
     *
     * @return static
     */
    public function setLocator(UniformResourceLocator $locator)
    {
        $this->locator = $locator;
        return $this;
    }
}