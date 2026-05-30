<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Domain\Models\AccessLogModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AccessLogModel $accessLogModel
    ) {}

    public function process(ServerRequestInterface $req, RequestHandlerInterface $handler): ResponseInterface
    {
        $res = $handler->handle($req);

        $method = $req->getMethod();
        $uri = $req->getUri()->getPath();
        $query = $req->getQueryParams();

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $status = $res->getStatusCode();

        $userId = $req->getAttribute('token_user_id');
        $email  = $req->getAttribute('token_email');
        $role   = $req->getAttribute('token_role');

        $userMetaData = [
            'user_id'     => $userId,
            'email'       => $email,
            'user_role'   => $role,
            'method'      => $method,
            'uri'         => $uri,
            'ip_address'  => $ip,
            'user_agent'  => $userAgent,
            'status_code' => $status,
            'query_params' => json_encode($query),
            'user_action' => sprintf("%s %s %s %d", $method, $uri, $ip, $status),
        ];

        $this->accessLogModel->insertLog($userMetaData);

        return $res;
    }
}
