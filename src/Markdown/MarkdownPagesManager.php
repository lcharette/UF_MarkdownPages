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

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Interop\Container\ContainerInterface;
use UserFrosting\Support\Exception\FileNotFoundException;

/**
 *   MarkdownPagesManager.
 */
class MarkdownPagesManager
{
    /**
     * @var ContainerInterface The DI Container
     */
    protected $ci;

    /**
     *    @var Filesystem
     */
    protected $filesystem;

    /**
     *    Constructor.
     *
     *    @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->filesystem = new Filesystem();
    }

    /**
     *    Get a list of all the files found across all active sprinkles
     *    TODO : Cache the result.
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
     *    Return an instance for a given page.
     *
     *    @param  string $path The page path
     *
     *    @return MarkdownPageInterface The page instance
     */
    public function getPage($path)
    {
        return new MarkdownPage($path, $this->ci->markdown, $this->ci->cache);
    }

    /**
     *    Finds a page in the available list by searching for the reltive path
     *    after `pages/`.
     *
     *    @param  string $slug The page slug, aka the part of url after the base `pages/` path
     *
     *    @throws FileNotFoundException If page is not found
     *
     *    @return MarkdownPageInterface The page instance
     */
    public function findPage($slug)
    {
        // Get all pages
        $pages = $this->getPages();

        // Find the page we want. Make sure we get a result,
        // otherwise file is not found
        $page = $pages->where('slug', $slug)->first();
        if (!$page) {
            throw new FileNotFoundException();
        }

        return $page;
    }

    /**
     *    Get a list of all the pages found across all active sprinkles.
     *    Returns a collection of MarkdownPage to which are added some custom
     *    public property, such as the relative path and the page url.
     *    TODO : Cache the result.
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
            $path = $locator->findResource('extra://pages/'.$filePath);

            // Get the page instance
            $page = $this->getPage($path);

            // Add the relative path
            $page->relativePath = $filePath;

            // Add the slug
            $page->slug = $this->pathToSlug($page->relativePath);

            // Add the url
            /*
            * TODO: Replace with the proper router `pathFor` method once UF issue #854 is resolve
            * @see https://github.com/userfrosting/UserFrosting/issues/854
            */
            //$page->url = $router->pathFor('markdownPages', ['path' => $page->slug]);
            $page->url = "/{$this->ci->config['MarkdownPages.route']}/{$page->slug}";

            // To help with the tree generation, we'll add the parent slug here
            $page->parent = $this->getParentSlug($page->slug);

            // Add page to the collection
            $pages->push($page);
        }

        return $pages;
    }

    /**
     *    Function that return the complete page tree used to create a menu.
     *
     *    @param  string $topLevel The top level slug (default '');
     *
     *    @return Collection
     */
    public function getTree($topLevel = '')
    {
        // Get all pages
        $pages = $this->getPages();

        //Sort the collection
        $pages = $pages->sortBy('relativePath');

        // We start by top most pages (the one without parent) and go down from here
        return $this->getPagesChildren($pages, $topLevel);
    }

    /**
     *    Set breadcrumbs recursively for the specified page and it's parent.
     *
     *    @param MarkdownPageInterface $page
     */
    public function setBreadcrumbs(MarkdownPageInterface $page)
    {
        // Add it to the breadcrumb
        $this->ci->breadcrumb->prepend($page->getTitle(), $page->url);

        // If the page doesn't have a parent, stop here
        if ($page->parent == '') {
            return;
        }

        // Find the parent instance
        $parent = $this->findPage($page->parent);

        // Add the parent's parent
        $this->setBreadcrumbs($parent);
    }

    /**
     *    Function that recursively find children for a given parent slug.
     *
     *    @param  Collection $pages A collection of pages
     *    @param  string $parentSlug The parent slug
     *
     *    @return Collection The tree of children for that given slug
     */
    protected function getPagesChildren($pages, $parentSlug)
    {
        // Regroups pages by parents
        $parents = $pages->groupBy('parent');

        // Get children for said parent. If none are found, we'll use an empty collection
        $children = $parents->get($parentSlug, function () {
            return collect([]);
        });

        // Loop all pages with said parent to recursively add them to the children's children
        foreach ($pages->where('parent', $parentSlug) as $child_page) {
            $child_page->children = $this->getPagesChildren($pages, $child_page->slug);
        }

        // Return the children, with the top level slug as a key
        return $children->keyBy(function ($page) {
            $parts = explode('/', $page->slug);

            return array_pop($parts);
        });
    }

    /**
     *    Return the parent of a given page slug.
     *
     *    @param  string $slug The item slug
     *
     *    @return string The item parent slug
     */
    protected function getParentSlug($slug)
    {
        $fragments = explode('/', $slug);
        array_pop($fragments);

        return implode('/', $fragments);
    }

    /**
     *    Return the ressouce locator.
     *
     *    @return \RocketTheme\Toolbox\ResourceLocator\UniformResourceLocator
     */
    protected function getLocator()
    {
        return $this->ci->locator;
    }

    /**
     *    Return the router service.
     *
     *    @return \UserFrosting\Sprinkle\Core\Router
     */
    protected function getRouter()
    {
        return $this->ci->router;
    }

    /**
     *    Convert a relative path to the url slug.
     *
     *    @param  string $relativePath The relative path
     *
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

    /**
     *    Return all pages inside a given directory.
     *
     *    @param  string $directory The absolute path to a directory
     *
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
