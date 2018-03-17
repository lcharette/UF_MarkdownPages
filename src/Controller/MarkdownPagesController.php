<?php
/**
 *    UF MarkdownPages.
 *
 *    @author Louis Charette
 *    @copyright Copyright (c) 2018 Louis Charette
 *
 *    @link      https://github.com/lcharette/UF_MarkdownPages
 *
 *    @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Account\Authenticate\Exception\AuthExpiredException;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Sprinkle\MarkdownPages\Markdown\MarkdownPagesManager;

/**
 *    MarkdownPagesController Class
 *    Controller class for the 'MarkdownPages' views.
 */
class MarkdownPagesController extends SimpleController
{
    /**
     *    Display Markdown based pages.
     *
     *    @param  Request $request
     *    @param  Response $response
     *    @param  array $args
     *
     *    @return void
     */
    public function displayPage(Request $request, Response $response, $args)
    {
        /** @var /UserFrosting\Sprinkle\Core\Router $router */
        $router = $this->ci->router;

        // Create manager instance
        $manager = new MarkdownPagesManager($this->ci);

        // Get path. If we have a trailling slash, we redirect to non trailing version
        if (substr($request->getUri(), -1) == '/') {
            return $response->withRedirect(rtrim($request->getUri(), '/'), 302);
        }

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
        $manager->setBreadcrumbs($file);

        // We'll try to find the right template
        $template = $file->getTemplate();

        // Render the page
        $this->ci->view->render($response, "markdownPages/$template.html.twig", [
           'content'    => $file->getContent(),
           'metadata'   => $metadata,
        ]);
    }

    /**
     *    Redirector when accessing the default route (`/p/` by default).
     *
     *    @param  Request $request
     *    @param  Response $response
     *    @param  array $args
     *
     *    @return void
     */
    public function redirectPlaceholderPage(Request $request, Response $response, $args)
    {
        /** @var \UserFrosting\Support\Repository\Repository $config */
        $config = $this->ci->config;

        /** @var /UserFrosting\Sprinkle\Core\Router $router */
        $router = $this->ci->router;

        // Get default page
        $pageName = $config['MarkdownPages.defaultPage'];

        // Get page route
        $route = $router->pathFor('markdownPages', ['path' => $pageName]);

        // Redirect
        return $response->withRedirect($route, 302);
    }
}
