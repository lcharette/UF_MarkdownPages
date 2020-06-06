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
     * Finds a file in the available list by searching for a specific slug.
     *
     * @param string $slug The page slug, aka the part of url after the base `pages/` path
     *
     * @throws FileNotFoundException If page is not found
     *
     * @return ResourceInterface The file resource
     */
    public function findFile(string $slug): ResourceInterface
    {
        // Get all pages
        $files = $this->getFiles();

        // Find the page we want. Make sure we get a result,
        // otherwise file is not found
        if (!isset($files[$slug])) {
            throw new FileNotFoundException();
        }

        return $files[$slug];
    }

    /**
     * Finds a page in the available list by searching for a specific slug.
     *
     * @param string $slug The page slug, aka the part of url after the base `pages/` path
     *
     * @throws FileNotFoundException If page is not found
     *
     * @return PageInterface The page instance
     */
    public function findPage(string $slug): PageInterface
    {
        $file = $this->findFile($slug);

        return $this->getPage($file);
    }

    /**
     * Convert a list of resources associated to their slugs.
     *
     * @return array<string,ResourceInterface> as [Slug => Resource]
     */
    public function getFiles(): array
    {
        $result = [];
        $files = $this->fetchFilesList();

        foreach ($files as $file) {
            $slug = $this->pathToSlug($file->getBasePath());
            $result[$slug] = $file;
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @return mixed[]
     */
    public function getNodes(): array
    {
        $result = [];

        foreach ($this->getFiles() as $slug => $file) {

            // Get page
            $page = $this->getPage($file);

            $result[$slug] = [
                'slug'     => $slug,
                'title'    => $page->getTitle(),
                'url'      => $this->router->pathFor('markdownPages', ['path' => $slug]),
                'parent'   => $this->getParentSlug($slug),
                'metadata' => $page->getMetadata(),
                'children' => [],
            ];
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param string|null $start
     *
     * @return mixed[]
     */
    public function getTree(?string $start = null): array
    {
        $nodes = $this->getNodes();

        return $this->getChildrenForParentSlug($nodes, $start);
    }

    /**
     * Undocumented function
     *
     * @param mixed[]     $nodes
     * @param string|null $parentSlug
     *
     * @return mixed[]
     */
    protected function getChildrenForParentSlug(array $nodes, ?string $parentSlug)
    {
        // Get children of passed parents
        $children = $this->getNodesForParent($nodes, $parentSlug);

        // Loop all pages with said parent to recursively add them to the children's children
        foreach ($children as $slug => $node) {
            $children[$slug]['children'] = $this->getChildrenForParentSlug($nodes, $slug);
        }

        return $children;
    }

    /**
     * Undocumented function
     *
     * @param mixed[]     $nodes
     * @param string|null $request
     *
     * @return mixed[]
     */
    protected function getNodesForParent(array $nodes, ?string $request)
    {
        $result = [];

        foreach ($nodes as $slug => $node) {
            if ($node['parent'] == $request) {
                $result[$slug] = $node;
            }
        }

        return $result;
    }

    /**
     * Get a list of all the resources found across all active sprinkles.
     *
     * @return ResourceInterface[]
     */
    protected function fetchFilesList(): array
    {
        return $this->locator->listResources($this->scheme);
    }

    /**
     * Return the parent of a given page slug.
     *
     * @param string $slug The slug
     *
     * @return string The slug parent slug
     */
    protected function getParentSlug(string $slug): string
    {
        $fragments = explode('/', $slug);
        array_pop($fragments);

        return implode('/', $fragments);
    }

    /**
     * Return an instance for a given page.
     *
     * @param string $path The page path
     *
     * @return MarkdownFile The page instance
     */
    protected function getPage(string $path): MarkdownFile
    {
        return new MarkdownFile($path, $this->parser, $this->filesystem);
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
}
