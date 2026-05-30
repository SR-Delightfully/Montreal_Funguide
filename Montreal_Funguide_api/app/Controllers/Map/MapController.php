<?php

namespace App\Controllers\Map;

use App\Controllers\BaseController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MapController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        try {
            return $this->renderJson($response, [
                "status" => "success",
                "data" => [
                    "message" => "Map root"
                ]
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Map failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
