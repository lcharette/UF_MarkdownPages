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

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use UserFrosting\Sprinkle\Core\Router;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\MarkdownFile;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Page\PageInterface;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\UniformResourceLocator\ResourceInterface;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * PagesManager
 */
class PagesManager
{
    /**
     * @var string The locator scheme for the files.
     */
    protected $scheme = 'markdown://';

    /**
     * @var ResourceLocatorInterface
     */
    protected $locator;

    /**
     * @var Parsedown
     */
    protected $parser;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Class constructor.
     *
     * @param ResourceLocatorInterface $locator
     * @param Parsedown                $parser
     * @param Filesystem|null          $filesystem
     */
    public function __construct(ResourceLocatorInterface $locator, Parsedown $parser, Router $router, ?Filesystem $filesystem = null)
    {
        $this->locator = $locator;
        $this->parser = $parser;
        $this->router = $router;

        if (is_null($filesystem)) {
            $this->filesystem = new Filesystem();
        } else {
            $this->filesystem = $filesystem;
        }
    }

    /**
     * Get a list of all the files found across all active sprinkles
     *
     * @return ResourceInterface[] An array of absolute paths
     */
    public function getFiles(): array
    {
        return $this->locator->listResources($this->scheme);
    }

    /**
     * Return an instance for a given page.
     *
     * @param string $path The page path
     *
     * @return MarkdownFile The page instance
     */
    public function getPage(string $path): MarkdownFile
    {
        return new MarkdownFile($path, $this->parser, $this->filesystem);
    }

    /**
     * Finds a page in the available list by searching for the reltive path
     * after `pages/`.
     *
     * @param string $slug The page slug, aka the part of url after the base `pages/` path
     *
     * @throws FileNotFoundException If page is not found
     *
     * @return PageNode The page instance
     */
    public function findPage(string $slug): PageNode
    {
        // Get all pages
        $pages = $this->getTree();
        $page = $pages->forSlug($slug);

        // Find the page we want. Make sure we get a result,
        // otherwise file is not found
        if (is_null($page)) {
            throw new FileNotFoundException();
        }

        return $page;
    }

    /**
     * Get a list of all the pages found across all active sprinkles.
     * Returns a collection of MarkdownPage to which are added some custom
     * public property, such as the relative path and the page url.
     * TODO : Cache the result.
     *
     * @return PagesTree
     */
    public function getTree(): PagesTree
    {
        $files = $this->getFiles();

        $collection = new PagesTree();

        foreach ($files as $file) {
            $page = $this->getPageNode($file);
            $collection->add($page);
        }

        return $collection;
    }

    public function getPageNode(ResourceInterface $file): PageNode
    {
        // Get the page instance
        $markdown = $this->getPage($file->getAbsolutePath());

        // Add the slug
        $slug = $this->pathToSlug($file->getBasePath());

        $page = new PageNode($markdown, $slug);

        // Add the url
        $url = $this->router->pathFor('markdownPages', ['path' => $slug]);
        $page->setUrl($url);

        // To help with the tree generation, we'll add the parent slug here
        $parent = $this->getParentSlug($slug);
        $page->setParent($parent);

        return $page;
    }

    /**
     * Function that return the complete page tree used to create a menu.
     *
     * @param string $topLevel The top level slug (default '');
     *
     * @return Collection
     */
    /*public function getTree($topLevel = '')
    {
        // Get all pages
        $pages = $this->getTree();

        //Sort the collection
        $pages = $pages->sortBy('relativePath');

        // We start by top most pages (the one without parent) and go down from here
        return $this->getPagesChildren($pages, $topLevel);
    }*/

    /**
     * Set breadcrumbs recursively for the specified page and it's parent.
     *
     * @param PageInterface $page
     */
    /*public function setBreadcrumbs(PageInterface $page)
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
    }*/

    /**
     * Set the value of scheme
     *
     * @param string $scheme
     *
     * @return self
     */
    public function setScheme(string $scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @return Parsedown
     */
    public function getParser(): Parsedown
    {
        return $this->parser;
    }

    /**
     * Function that recursively find children for a given parent slug.
     *
     * @param Collection $pages      A collection of pages
     * @param string     $parentSlug The parent slug
     *
     * @return Collection The tree of children for that given slug
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
     * Return the parent of a given page slug.
     *
     * @param string $slug The item slug
     *
     * @return string The item parent slug
     */
    protected function getParentSlug($slug)
    {
        $fragments = explode('/', $slug);
        array_pop($fragments);

        return implode('/', $fragments);
    }

    /**
     * Convert a relative path to the url slug.
     *
     * @param string $relativePath The relative path
     *
     * @return string The slug
     */
    protected function pathToSlug($relativePath)
    {
        $dir = pathinfo($relativePath, PATHINFO_DIRNAME);

        // We need to remove the order number form the path to get the slug
        $dirFragments = explode('/', $dir);

        foreach ($dirFragments as $key => $fragment) {
            $fragmentList = explode('.', $fragment);

            if (count($fragmentList) == 1) {
                $dirFragments[$key] = $fragmentList[0];
            } else {
                $dirFragments[$key] = $fragmentList[1];
            }
        }

        // Glue the fragments back together
        return implode('/', $dirFragments);
    }
}
