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

use Illuminate\Filesystem\Filesystem;
use InvalidArgumentException;
use League\Csv\InvalidArgument;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\Parser\Parsedown;
use UserFrosting\Support\Exception\FileNotFoundException;
use UserFrosting\UniformResourceLocator\ResourceInterface;
use UserFrosting\UniformResourceLocator\ResourceLocatorInterface;

/**
 * Markdown File implementation of the Page Interface.
 * Creates a Page Interface from a file formatted as Markdown.
 *
 * N.B.: This class can't locate any files from the filesystem.
 * It simply check if the file exist and that's it's a markdown file.
 * To find files, get it's relative path or url, MarkdownPagesManager can
 * help.
 */
class MarkdownFile implements PageInterface
{
    /**
     * @var string The file raw content
     */
    protected $rawContent;

    /**
     * @var ResourceInterface
     */
    protected $resource;

    /**
     * @var Parsedown The markdown parser
     */
    protected $parser;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string[] Local cache of the file metadata
     */
    protected $metadata;

    /**
     * @var array<string,mixed>
     */
    protected $custom_metadata = [];

    /**
     * Class constructor.
     *
     * @param ResourceInterface $resource
     * @param Parsedown $parser
     * @param Filesystem|null $filesystem
     */
    public function __construct(ResourceInterface $resource, Parsedown $parser, ?Filesystem $filesystem = null)
    {
        if (is_null($filesystem)) {
            $filesystem = new Filesystem();
        }

        $this->resource = $resource;
        $this->parser = $parser;
        $this->filesystem = $filesystem;

        // Load the file
        $this->load();
    }

    /**
     * Load the file content.
     * The content of the file will be loaded into `$this->rawContent`
     *
     * @throws FileNotFoundException    if file is not found;
     * @throws InvalidArgumentException if file is not plain/text or .md
     *
     * @todo : Cache the result using the Cache facade.
     */
    protected function load(): void
    {
        // Make sure the page exist
        if (!$this->filesystem->exists($this->resource)) {
            throw new FileNotFoundException();
        }

        $extension = $this->resource->getExtension();
        $mimeType = $this->filesystem->mimeType($this->resource);

        // Make sure file is markdown
        if ($extension != 'md' || $mimeType != 'text/plain') {
            $filename = $this->resource->getUri();

            throw new InvalidArgumentException("File `$filename` ($mimeType) doesn't seems to be a valid markdown file.");
        }

        // Get content
        $this->rawContent = $this->filesystem->get($this->resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata(): array
    {
        if (!isset($this->metadata)) {
            $this->metadata = $this->parser->meta($this->rawContent);
        }

        return array_replace_recursive($this->metadata, $this->custom_metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function addMetadata(string $slug, $value)
    {
        $this->custom_metadata[$slug] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        $metadata = $this->getMetadata();

        // Return empty if metadata doesn't exist
        if (array_key_exists('title', $metadata)) {
            return $metadata['title'];
        } else {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        $metadata = $this->getMetadata();

        // Return empty if metadata doesn't exist
        if (array_key_exists('description', $metadata)) {
            return $metadata['description'];
        } else {
            return '';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return basename($this->path, '.md');
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return $this->parser->text($this->rawContent);
    }

    public function getSlug(): string
    {
        $dir = pathinfo($this->getPath(), PATHINFO_DIRNAME);

        // We need to remove the order number form the path to get the slug
        $dirFragments = explode('/', $dir);

        foreach ($dirFragments as $key => $fragment) {
            $fragmentList = explode('.', $fragment);

            if (count($fragmentList) === 3) {
                $dirFragments[$key] = $fragmentList[1];
            } else {
                $dirFragments[$key] = $fragmentList[0];
            }
        }

        // Glue the fragments back together
        return implode('/', $dirFragments);
    }

    /**
     * Returns the page filename.
     *
     * @return string
     */
    public function getFilename(): string
    {
        return basename($this->path);
    }

    /**
     * Return the file path passed to the constructor.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
