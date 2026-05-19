<?php

declare(strict_types=1);

namespace App\Controllers\Fungi;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FungiController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, [
            "message" => "List all fungi"
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->renderJson($response, [
            "message" => "Create fungi"
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, [
            "fungi_id" => (int)$args['id']
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, [
            "message" => "Update fungi",
            "fungi_id" => (int)$args['id']
        ]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, [
            "message" => "Delete fungi",
            "fungi_id" => (int)$args['id']
        ]);
    }
}