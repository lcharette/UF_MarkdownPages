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

use Illuminate\Support\Collection;

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
     *    Get a list of all the files found across all active sprinkles
     *    TODO : Cache the result
     *
     *    @return array An array of absolute paths
     */
    public function getFiles()
    {
        /** @var \RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator $locator */
        $locator = $this->getLocator();

        // Get all markdown pages
        // N.B.: Replace with custom locator url once UF issue #853 is resolve
        // @see https://github.com/userfrosting/UserFrosting/issues/853
        $paths = $locator->findResources('extra://pages/');

        $pages = [];
        foreach ($paths as $path) {
            $pathPages = $this->getPagesFromDirectory($path);
            $pages = array_merge($pathPages, $pages);
        }

        // Remove duplicates
        $pages = array_unique($pages);

        return $pages;
    }

    /**
     *    Return an instance for a given page
     *
     *    @param  string $path The page path
     *    @return MarkdownPageInterface The page instance
     */
    public function getPage($path)
    {
        return new MarkdownPage($path, $this->ci->cache);
    }

    /**
     *    Finds a page in the available list by searching for the reltive path
     *    after `pages/`
     *
     *    @param  string $slug The page slug, aka the part of url after the base `pages/` path
     *    @return MarkdownPageInterface The page instance
     *    @throws FileNotFoundException If page is not found
     */
    public function findPage($slug)
    {
        // Get all pages
        $pages = $this->getPages();

        // Find the page we want. Make sure we get a result,
        // otherwise file is not found
        $page = $pages->where('slug', $slug)->first();
        if (!$page) {
            throw new FileNotFoundException;
        }

        return $page;
    }

    /**
     *    Get a list of all the pages found across all active sprinkles.
     *    Returns a collection of MarkdownPage to which are added some custom
     *    public property, such as the relative path and the page url.
     *    TODO : Cache the result
     *
     *    @return Collection
     */
    public function getPages()
    {
        /** @var \RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator $locator */
        $locator = $this->getLocator();

        /** @var \UserFrosting\Sprinkle\Core\Router $router */
        $router = $this->getRouter();

        // Get all the files
        $files = $this->getFiles();

        // Create a collection for our tree
        $pages = collect();

        // Loop through files to populate pages
        foreach ($files as $filePath) {

            // Get the full absolute path
            $path = $locator->findResource('extra://pages/' . $filePath);

            // Get the page instance
            $page = $this->getPage($path);

            // Add the relative path
            $page->relativePath = $filePath;

            // Add the slug
            $page->slug = $this->pathToSlug($page->relativePath);

            // Add the url
            //$page->url = $router->pathFor('markdownPages', ['path' => $page->slug]);

            // Add page to the collection
            $pages->push($page);
        }

        return $pages;
    }

    /**
     *    Return the ressouce locator
     *    @return \RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator
     */
    protected function getLocator()
    {
        return $this->ci->locator;
    }

    /**
     *    Return the router service
     *    @return \UserFrosting\Sprinkle\Core\Router
     */
    protected function getRouter()
    {
        return $this->ci->router;
    }

    /**
     *    Convert a relative path to the url slug
     *
     *    @param  string $relativePath The relative path
     *    @return string The slug
     */
    protected function pathToSlug($relativePath)
    {
        $dir = $this->filesystem->dirname($relativePath);

        // We need to remove the order number form the path to get the slug
        $dirFragments = explode('/', $dir);
        foreach ($dirFragments as $key => $fragment) {
            $fragmentList = explode('.', $fragment);
            $dirFragments[$key] = $fragmentList[1];
        }

        // Glue the fragments back together
        return implode('/', $dirFragments);
    }

    public function getTree()
    {
        //return array - nested list of pages. Needs metadata of each pages
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

        $pages = array_map(function ($page) {
            return $page->getRelativePathname();
        }, $pages);

        return $pages;
    }
}