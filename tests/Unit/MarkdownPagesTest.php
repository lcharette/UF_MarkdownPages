<?php
/**
*    UF MarkdownPages
*
*    @author Louis Charette
*    @copyright Copyright (c) 2018 Louis Charette
*    @link      https://github.com/lcharette/UF_MarkdownPages
*    @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
*/
namespace UserFrosting\Tests\Unit;

use Mockery as m;
use InvalidArgumentException;
use UserFrosting\Tests\TestCase;
use UserFrosting\Sprinkle\MarkdownPages\MarkdownPage;
use UserFrosting\Sprinkle\MarkdownPages\MarkdownPagesManager;
use UserFrosting\Support\Exception\FileNotFoundException;

/**
 *    Tests for MarkdownPages Sprinkle
 */
class MarkdownPagesTest extends TestCase
{
    /**
     *    @var MarkdownPagesManager
     */
    protected $manager;

    /**
     *    @var string The test page relative path
     */
    protected $testPage = 'app/sprinkles/MarkdownPages/tests/Unit/test.md';

    /**
     *    @var string The no metadata test page relative path
     */
    protected $testPageNoMetadata = 'app/sprinkles/MarkdownPages/tests/Unit/test-noMetadata.md';

    /**
     *    @inheritDoc
     */
    protected function setUp()
    {
        // Setup parent first to get access to the container
        parent::setUp();

        // Setup manager
        $this->manager = new MarkdownPagesManager($this->ci);
    }

    /**
     *    @inheritDoc
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     *    Make sure the instances was created successfully
     */
    public function testManagerInstance()
    {
        $this->assertInstanceOf(MarkdownPagesManager::class, $this->manager);
    }

    /**
     *    Test the custom locator
     *    TODO : Change to custom `pages://` location. This requires
     *           UserFrosting issue #853 to be fixed.
     *    @see   https://github.com/userfrosting/UserFrosting/issues/853
     *
     */
    public function testLocator()
    {
        $locator = $this->ci->locator;
        $path = $locator->findResources('extra://pages/');
        $this->assertInternalType('array', $path);
    }

    /**
     *    Test the `getPages` method
     */
    public function testMarkdownPagesManager_getPages()
    {
        $pages = $this->manager->getPages();
        $this->assertInternalType('array', $pages);
    }

    /**
     *    Test if the manager return the correct thing when given a full path
     */
    public function testMarkdownPagesManager_getPage()
    {
        $page = $this->manager->getPage($this->testPage);
        $this->assertInstanceOf(MarkdownPage::class, $page);

        // When dealing with a non existing page, an exception should occur
        $this->expectException(FileNotFoundException::class);
        $page = $this->manager->getPage('undefined.md');

        // When dealing with a non markdown file, an exception should occur
        $this->expectException(InvalidArgumentException::class);
        $page = $this->manager->getPage('test.txt');
    }

    /**
     *    Test the MarkdownPage class using the test page
     */
    public function test_MarkdownPage()
    {
        $page = new MarkdownPage($this->ci->cache, $this->testPage);
        $this->assertInstanceOf(MarkdownPage::class, $page);

        // Test metadata
        $metadata = $page->getMetadata();
        $this->assertInternalType('array', $metadata);
        $this->assertEquals('Test page', $metadata['title']);
        $this->assertEquals('Test page', $page->getTitle());
        $this->assertEquals('The test page description', $metadata['description']);
        $this->assertEquals('The test page description',  $page->getDescription());

        // Get filename and path
        $this->assertEquals('test.md',  $page->getFilename());
        $this->assertEquals('test',  $page->getTemplate());
        $this->assertEquals($this->testPage,  $page->getPath());

        // Test data
        $content = $page->getContent();
        $this->assertInternalType('string', $content);
    }

    /**
     *    Test with a second file with no metadata
     */
    public function test_MarkdownPage_noMetadata()
    {
        $page = new MarkdownPage($this->ci->cache, $this->testPageNoMetadata);
        $this->assertInstanceOf(MarkdownPage::class, $page);

        // Test metadata
        $metadata = $page->getMetadata();
        $this->assertInternalType('array', $metadata);
        $this->assertArrayNotHasKey('title', $metadata);
        $this->assertEquals('', $page->getTitle());
        $this->assertArrayNotHasKey('description', $metadata);
        $this->assertEquals('',  $page->getDescription());

        // Get filename and path
        $this->assertEquals('test-noMetadata.md',  $page->getFilename());
        $this->assertEquals('test-noMetadata',  $page->getTemplate());
        $this->assertEquals($this->testPageNoMetadata,  $page->getPath());

        // Test data
        $content = $page->getContent();
        $this->assertInternalType('string', $content);
        // Actually check the result this time
        $this->assertEquals('<p>Hello <em>World</em>!</p>', $content);
    }
}