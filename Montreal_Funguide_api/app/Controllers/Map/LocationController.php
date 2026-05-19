<?php

namespace App\Controllers\Map;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LocationController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["locations" => []]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "Create location"]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["location_id" => $args['id']]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Update location"]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Delete location"]);
    }
}