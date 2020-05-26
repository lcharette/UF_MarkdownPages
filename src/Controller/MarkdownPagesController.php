<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Controller;

use Slim\Http\Response;
use Slim\Http\Request;
use UserFrosting\Sprinkle\Account\Authenticate\Exception\AuthExpiredException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\MarkdownPages;

/**
 * MarkdownPagesController Class
 * Controller class for the 'MarkdownPages' views.
 */
class MarkdownPagesController extends SimpleController
{
    /**
     * Display Markdown based pages.
     *
     * @param Request  $request
     * @param Response $response
     * @param string[] $args
     */
    public function displayPage(Request $request, Response $response, array $args): Response
    {
        // Get path. If we have a trailling slash, we redirect to non trailing version
        if (substr($request->getUri(), -1) == '/') {
            return $response->withRedirect(rtrim($request->getUri(), '/'), 302);
        }

        /** @var \UserFrosting\Sprinkle\Core\Router */
        $router = $this->ci->router;

        // Create manager instance
        $manager = $this->ci->markdownPages;

        // Get the file instance. A file not found exception will be thrown
        // if the page doesn't exist
        $file = $manager->findPage($args['path']);

        // Get the file metadata
        $metadata = $file->getMetadata();

        // If file has a redirect metadata, perform the redirect
        if (isset($metadata['redirect'])) {
            $redirect = trim($metadata['redirect']);

            // We try to find the page. If it doesn't exist, we redirect directly
            try {
                $redirectFile = $manager->findPage($redirect);
                $path = $router->pathFor('markdownPages', ['path' => $redirect]);
            } catch (\Exception $e) {
                $path = $redirect;
            }

            return $response->withRedirect($path, 302);
        }

        // Check if page metadata requires auth.
        if (isset($metadata['authGuard']) && $metadata['authGuard'] == true) {
            if (!$this->ci->authenticator->check()) {
                throw new AuthExpiredException();
            }
        }

        // We also need to find and set the breadcrumbs
        // $manager->setBreadcrumbs($file);

        // We'll try to find the right template
        $template = $file->getTemplate();

        // Render the page
        return $this->ci->view->render($response, "markdownPages/$template.html.twig", [
            'content'    => $file->getContent(),
            'metadata'   => $metadata,
        ]);
    }

    /**
     * Redirector when accessing the default route (`/p/` by default).
     *
     * @param Request  $request
     * @param Response $response
     */
    public function redirectPlaceholderPage(Request $request, Response $response): Response
    {
        /** @var \UserFrosting\Support\Repository\Repository */
        $config = $this->ci->config;

        /** @var \UserFrosting\Sprinkle\Core\Router */
        $router = $this->ci->router;

        // Get page route
        $route = $router->pathFor('markdownPages', [
            'path' => $config['MarkdownPages.defaultPage'],
        ]);

        // Redirect
        return $response->withRedirect($route, 302);
    }
}
