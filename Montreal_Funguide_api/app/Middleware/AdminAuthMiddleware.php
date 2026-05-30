<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use Slim\Exception\HttpForbiddenException;

class AdminAuthMiddleware implements MiddlewareInterface
{
    /**
     * Process the request - check if user is authenticated AND is an admin.
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // getting the user's role from the JWT claims attached
        // by the AuthMiddleware:
        $role = $request->getAttribute('token_role');

        // checking if the user is an admin:
        if ($role !== 'admin') {

            throw new HttpForbiddenException(
                $request,
                "Access denied. Admin privileges required."
            );
        }

        // Passing request to the next handler & returning the response
        return $handler->handle($request);
    }
}
