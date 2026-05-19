<?php

namespace App\Controllers\Map;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MapController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "Map root"]);
    }
}