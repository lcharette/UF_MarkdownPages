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

use UserFrosting\Tests\TestCase;

/**
 *    Tests pour les collections personnalisé
 */
class MarkdownPagesTest extends TestCase
{
    public function testLocator()
    {
        $locator = $this->ci->locator;
        $path = $locator->findResource('pages://', true, true);
        $this->assertInternalType([], $path);
    }
}