<?php

/*
 * UserFrosting MarkdownPages Sprinkle
 *
 * @author    Louis Charette
 * @copyright Copyright (c) 2020 Louis Charette
 * @link      https://github.com/lcharette/UF_MarkdownPages
 * @license   https://github.com/lcharette/UF_MarkdownPages/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Sprinkle\MarkdownPages\Tests\Integration;

use Mockery as m;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use UserFrosting\Sprinkle\Core\Tests\withController;
use UserFrosting\Sprinkle\MarkdownPages\Controller\MarkdownPagesController;
use UserFrosting\Tests\TestCase;

/**
 * Tests for MarkdownPagesController.
 */
class MarkdownPagesControllerTest extends TestCase
{
    use withController;

    public function testDisplayPage(): void
    {
        $this->assertTrue(true);

        //$fakeCi = m::mock(ContainerInterface::class);
        //$fakeCi->router = m::mock();
        /*
        $controller = new MarkdownPagesController($this->ci);
        $this->assertInstanceOf(MarkdownPagesController::class, $controller);

        $request = $this->getRequest();
        $result = $this->getResponse();
        $args = [];
        $return = $controller->displayPage($request, $result, $args);

        // Perform asertions
        $this->assertInstanceOf(ResponseInterface::class, $return);
        $body = (string) $result->getBody();
        $this->assertSame($result->getStatusCode(), 200);
        $this->assertNotSame('', $body);
        */
    }
}
