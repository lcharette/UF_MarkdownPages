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

use InvalidArgumentException;
use Illuminate\Cache\Repository as Cache;
use Illuminate\Filesystem\Filesystem;
use Pagerange\Markdown\MetaParsedown;
use UserFrosting\Sprinkle\MarkdownPages\MarkdownPageInterface;
use UserFrosting\Support\Exception\FileNotFoundException;

/**
 *   MarkdownPage
 */
class MarkdownPage implements MarkdownPageInterface
{
    /**
     * @var Cache The cache service instance
     */
    protected $cache;

    /**
     * @var string The file path
     */
    protected $path;

    /**
     * @var array The file metadata
     */
    protected $metadata;

    /**
     * @var string The file raw content
     */
    protected $rawContent;

    /**
     *    @var Filesystem
     */
    protected $filesystem;

    /**
     *    @var MetaParsedown The markdown parser
     */
    protected $parser;

    /**
     *    Constructor
     *
     *    @param Cache $cache The cache service
     *    @param string $path The file full path
     */
    public function __construct(Cache $cache, $path)
    {
        $this->cache = $cache;
        $this->path = $path;

        // Create a Filesystem instance
        $this->filesystem = new Filesystem;

        // Create Markdown parser instance
        $this->parser = new MetaParsedown();

        // Make sure the page exist
        if (!$this->filesystem->exists($this->path)) {
            throw new FileNotFoundException;
        }

        // Make sure file is markdown
        if ($this->filesystem->extension($this->path) != 'md' || $this->filesystem->mimeType($this->path) != 'text/plain') {
            $filename = basename($path);
            throw new InvalidArgumentException("File `$filename` doesn't seems to be a valid markdown file.");
        }

        // Load the page
        $this->load();
    }

    /**
     *    Load the file content
     *    TODO : Cache the result
     *
     *    @return void
     */
    protected function load()
    {
        $this->rawContent = $this->filesystem->get($this->path);
    }

    /**
     *    Returns the file metadata
     *
     *    @return array
     */
    public function getMetadata()
    {
        return $this->parser->meta($this->rawContent);
    }

    /**
     *    Returns the file title
     *
     *    @return string The file title
     */
    public function getTitle()
    {
        $metadata = $this->getMetadata();
        return (array_key_exists('title', $metadata)) ? $metadata['title'] : '';
    }

    /**
     *    Returns the file description
     *
     *    @return string The file description
     */
    public function getDescription()
    {
        $metadata = $this->getMetadata();
        return (array_key_exists('description', $metadata)) ? $metadata['description'] : '';
    }

    /**
     *    Returns the page filename
     *    @return string
     */
    public function getFilename()
    {
        return basename($this->path);
    }

    /**
     *    Return the page template, aka the filename without the extension
     *
     *    @return string
     */
    public function getTemplate()
    {
        return basename($this->path, '.md');
    }

    /**
     *    Returns the file parsed content as HTML
     *    @return string The file content
     */
    public function getContent()
    {
        return $this->parser->text($this->rawContent);
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
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
}