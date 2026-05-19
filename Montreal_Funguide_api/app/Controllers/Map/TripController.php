<?php

namespace App\Controllers\Map;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TripController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["trips" => []]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "Create trip"]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["trip_id" => $args['id']]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Update trip"]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Delete trip"]);
    }
}