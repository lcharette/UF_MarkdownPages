<?php
/**
*    UF MarkdownPages
*
*    @author Louis Charette
*    @copyright Copyright (c) 2018 Louis Charette
*    @link      https://github.com/lcharette/UF_MarkdownPages
*    @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
*/
namespace UserFrosting\Sprinkle\MarkdownPages\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\MarkdownPagesManager;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;

/**
 * AnalyseController Class
 * Controller class for the 'analyse' views
 */
class MarkdownPagesController extends SimpleController
{
    /**
     *    Display page
     *
     *    @param  Request $request
     *    @param  Response $response
     *    @param  array $args
     *    @return void
     */
    public function displayPage(Request $request, Response $response, $args)
    {
        // Create manager instance
        $manager = new MarkdownPagesManager($this->ci);

        // Get the file instance. A file not found exception will be thrown
        // if the page doesn't exist
        $file = $manager->findPage($args['path']);

        // We also need to find and set the breadcrumbs
        $manager->setBreadcrumbs($file);

        // We'll try to find the right template
        $template = $file->getTemplate();

        // Render the page
        $this->ci->view->render($response, "markdownPages/$template.html.twig", [
           'content'    => $file->getContent(),
           'metadata'   => $file->getMetadata()
        ]);
    }
}