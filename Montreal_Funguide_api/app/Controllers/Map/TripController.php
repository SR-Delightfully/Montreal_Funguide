<?php

namespace App\Controllers\Map;

use App\Controllers\BaseController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class TripController extends BaseController
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
                "message" => "Failed to fetch trips",
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
                "message" => "Trip created",
                "data" => $data
            ], 201);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Trip creation failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid trip ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "data" => ["trip_id" => $id]
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Trip fetch failed",
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
                throw new HttpBadRequestException($request, "Invalid trip ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Trip updated",
                "trip_id" => $id,
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
                throw new HttpBadRequestException($request, "Invalid trip ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Trip deleted",
                "trip_id" => $id
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
