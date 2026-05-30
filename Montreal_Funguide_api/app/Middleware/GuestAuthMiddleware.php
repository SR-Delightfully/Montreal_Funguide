<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Routing\RouteContext;

use App\Helpers\Core\AppSettings;

class GuestAuthMiddleware implements MiddlewareInterface
{
    private AppSettings $settings;

    public function __construct(AppSettings $settings)
    {
        $this->settings = $settings;
    }

    public function process(
        Request $request,
        RequestHandler $handler
    ): Response {

        $authHeader = $request->getHeaderLine('Authorization');

        if (
            !empty($authHeader) &&
            str_starts_with($authHeader, 'Bearer ')
        ) {

            try {

                $jwt = str_replace(
                    'Bearer ',
                    '',
                    $authHeader
                );

                $jwt_secret = $this->settings->get('jwt');

                JWT::decode(
                    $jwt,
                    new Key($jwt_secret['secret'], 'HS256')
                );

                $routeParser = RouteContext::fromRequest($request)
                    ->getRouteParser();

                $dashboardUrl = $routeParser
                    ->urlFor('dashboard.load');

                $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();

                return $psr17Factory
                    ->createResponse(302)
                    ->withHeader('Location', $dashboardUrl);

            }
            catch (\Exception $e) {

                // invalid token = continue as guest

            }
        }

        return $handler->handle($request);
    }
}
