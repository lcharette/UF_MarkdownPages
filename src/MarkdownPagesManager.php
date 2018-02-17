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

use Illuminate\Filesystem\Filesystem;
use Interop\Container\ContainerInterface;
use UserFrosting\Support\Exception\FileNotFoundException;

/**
 *   MarkdownPagesManager
 */
class MarkdownPagesManager
{
    /**
     * @var ContainerInterface $ci The DI Container
     */
    protected $ci;

    /**
     *    @var Filesystem
     */
    protected $filesystem;

    /**
     *    Constructor
     *    @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->filesystem = new Filesystem;
    }

    /**
     *    Get a list of all the pages available across all active sprinkles
     *    TODO : Cache the result
     *
     *    @return array An array of absolute paths
     */
    public function getPages()
    {
        // Get all markdown pages
        // N.B.: Replace with locator once UF issue #853 is resolve
        // @see https://github.com/userfrosting/UserFrosting/issues/853
        //$paths = $this->locator->findResources('pages://');
        $paths = $this->getPaths();

        $pages = [];
        foreach ($paths as $path) {
            $pathPages = $this->getPagesFromDirectory($path);
            $pages = array_merge($pathPages, $pages);
        }

        return $pages;
    }

    /**
     *    Return an instance for a given page
     *
     *    @param  string $path The page path
     *    @return MarkdownPage The page instance
     */
    public function getPage($path)
    {
        return new MarkdownPage($this->ci->cache, $path);
    }

    /**
     *    Finds a page in the available list by searching for the reltive path
     *    after `pages/`
     *
     *    @param  string $path The page slug + relative path
     *    @return MarkdownPage The page instance
     *    @throws FileNotFoundException If page is not found
     */
    public function findPage($path)
    {
        // return MarkdownPage
    }

    public function getTree()
    {
        //return array - nested list of files. Needs metadata of each files
    }

    /**
     *    Return the page url
     *
     *    @param  MarkdownPage $page The page
     *    @return string The url
     */
    public function getPageUrl(MarkdownPage $page)
    {
    }

    /**
     *    Return all pages inside a given directory
     *    @param  string $directory The absolute path to a directory
     *    @return array
     */
    protected function getPagesFromDirectory($directory)
    {
        // If directory diesn't exist, stop
        if (!$this->filesystem->exists($directory)) {
            return [];
        }

        // Get pages
        $pages = $this->filesystem->allFiles($directory);

        // Transform the Symfony SplFileInfo object to string so we only get the path
        return array_map('strval', $pages);
    }

    /**
     *    Returns all the path where we can find pages
     *    N.B.: Replace with locator once UF issue #853 is resolve
     *    @see https://github.com/userfrosting/UserFrosting/issues/853
     *
     *    @return array List of absolute paths
     */
    public function getPaths()
    {
        $paths = [];
        foreach ($this->ci->sprinkleManager->getSprinkleNames() as $sprinkleName) {
            $paths[] = $this->pagesDirectoryPath($sprinkleName);
        }
        return $paths;
    }

    /**
     *    Return the path where we can find the pages in a given sprinkle
     *    N.B.: Replace with locator once UF issue #853 is resolve
     *    @see https://github.com/userfrosting/UserFrosting/issues/853
     *
     *    @param  string $sprinkleName The sprinkle name
     *    @return string The path where pages are stored
     */
    protected function pagesDirectoryPath($sprinkleName)
    {
        return \UserFrosting\SPRINKLES_DIR .
               \UserFrosting\DS .
               $sprinkleName .
               \UserFrosting\DS .
               \MarkdownPages\PAGES_PATH;
    }
}