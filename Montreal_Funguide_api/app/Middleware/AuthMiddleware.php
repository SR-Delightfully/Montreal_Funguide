<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Core\AppSettings;
use App\Helpers\FlashMessage;
// use App\Helpers\SessionManager; we don't need a session if we're using JWT.
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\RouteContext;
use App\Helpers\UserContext;
use Exception;
use stdClass;

// Make sure to install firebase/php-jwt
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ResponseInterface;

class AuthMiddleware implements MiddlewareInterface
{
    private AppSettings $settings;

    public function __construct(AppSettings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Process the request - check if user is authenticated.
     */
    public function process(Request $req, RequestHandler $handler): ResponseInterface
    {
        try {
            // getting the JWT secret:
            $jwt_secret = $this->settings->get('jwt');

            // getting the authorization header from the request ($req):
            $auth_header = $req->getHeaderLine('Authorization');

            if (
                empty($auth_header) ||
                !str_starts_with($auth_header, 'Bearer ')
            ) {
                throw new HttpUnauthorizedException(
                    $req,
                    "[Error] Token is required"
                );
            }

            // Extracting the token from said header:
            $jwt = str_replace("Bearer ", "", $auth_header);

            // Decoding & validating token with JWT::decode()
            $decode = JWT::decode(
                $jwt,
                new Key($jwt_secret['secret'], 'HS256')
            );

            // if the request is valid, attach decoded claims to it:
            $req = $req->withAttribute(
                'token_user_id',
                $decode->user_id ?? null
            );

            $req = $req->withAttribute(
                'token_email',
                $decode->user_email ?? null
            );

            // Passing modified request to the next handler & returning the response
            $res = $handler->handle($req);
            return $res;
        } catch (ExpiredException $e) {
            throw new HttpUnauthorizedException(
                $req,
                "[ERROR] Token expired"
            );
        } catch (SignatureInvalidException $e) {
            throw new HttpUnauthorizedException(
                $req,
                "[ERROR] Signature is invalid"
            );
        } catch (\UnexpectedValueException $e) {
            throw new HttpUnauthorizedException(
                $req,
                "[ERROR] Invalid token"
            );
        } catch (Exception $e) {
            throw new HttpUnauthorizedException(
                $req,
                "[ERROR] Authentication failed"
            );
        }
    }
}
