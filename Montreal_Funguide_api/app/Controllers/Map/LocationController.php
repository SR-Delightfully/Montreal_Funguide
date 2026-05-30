<?php

namespace App\Controllers\Map;

use App\Controllers\BaseController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class LocationController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        try {
            return $this->renderJson($response, [
                "status" => "success",
                "data" => []
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch locations",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            if (!$data) {
                throw new HttpBadRequestException($request, "Missing request body");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Location created",
                "data" => $data
            ], 201);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Create location failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid location ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "data" => ["location_id" => $id]
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch location",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);
            $data = $request->getParsedBody();

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid location ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Location updated",
                "location_id" => $id,
                "data" => $data
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Update failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid location ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Location deleted",
                "location_id" => $id
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Delete failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
