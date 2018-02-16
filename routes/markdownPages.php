<?php
 /**
 *    UF MarkdownPages
 *
 *    @author Louis Charette
 *    @copyright Copyright (c) 2018 Louis Charette
 *    @link      https://github.com/lcharette/UF_MarkdownPages
 *    @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
 */

use UserFrosting\Sprinkle\MarkdownPages\Controller\MarkdownPages;

$config = $app->getContainer()->get('config');
$app->get('/' . $config['MarkdownPages.route'] . '/{path:.+}', MarkdownPages::class . ':displayPage')->setName('markdownPages');