<?php
/**
*    UF MarkdownPages
*
*    @author Louis Charette
*    @copyright Copyright (c) 2018 Louis Charette
*    @link      https://github.com/lcharette/UF_MarkdownPages
*    @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/licenses.md (MIT License)
*/
namespace UserFrosting\Sprinkle\MarkdownPages\ServicesProvider;

use Interop\Container\ContainerInterface;

/**
 *    ServicesProvider class.
 *    Registers services for the MarkdownPages sprinkle.
 */
class ServicesProvider
{
    /**
     *    @param ContainerInterface $container
     */
    public function register(ContainerInterface $container)
    {
        /**
         *    Extends the 'locator' service with custom streams
         *    Custom stream added: pages://
         */
        // NB: This requires UserFrosting issue #853 to be fixed.
        // This service can't be extend as of now
        // @see: https://github.com/userfrosting/UserFrosting/issues/853
        /*$container->extend('streamBuilder', function ($app) {
            //$locator->addPath('pages', '', \UserFrosting\APP_DIR_NAME . '/pages');
            return $app['streamBuilder'];
        });*/
    }
}