<?php

declare(strict_types=1);

namespace App\Controllers\Fungi;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class HabitatController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "All habitats"]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "Create habitat"]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["habitat_id" => $args['id']]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Update habitat"]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Delete habitat"]);
    }

    public function showByFungi(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, [
            "fungi_id" => $args['id'],
            "habitat" => []
        ]);
    }
}