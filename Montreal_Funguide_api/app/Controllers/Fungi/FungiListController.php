<?php

declare(strict_types=1);

namespace App\Controllers\Fungi;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class FungiListController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, [
            "message" => "Filtered fungi list"
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->renderJson($response, [
            "message" => "Not used but required for REST consistency"
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, [
            "message" => "Single fungi list item",
            "id" => $args['id']
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Update list item"]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Delete list item"]);
    }
}