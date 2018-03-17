<?php

/*
 * UF MarkdownPages
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2018 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
 */

use UserFrosting\Sprinkle\MarkdownPages\Controller\MarkdownPagesController;

$config = $app->getContainer()->get('config');
$app->get('/'.$config['MarkdownPages.route'].'/{path:.+}[/]', MarkdownPagesController::class.':displayPage')->setName('markdownPages');

// Target the default route and redirect to index if we try to access it
$app->get('/'.$config['MarkdownPages.route'].'[/]', MarkdownPagesController::class.':redirectPlaceholderPage');
