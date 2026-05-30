<?php

declare(strict_types=1);

namespace App\Controllers\Fungi;

use App\Controllers\BaseController;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class FungiListController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        try {
            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Filtered fungi list",
                "data" => []
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch fungi list",
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
                "message" => "Fungi list item created (mock)",
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
                "message" => "Create failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid list ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "data" => [
                    "id" => $id
                ]
            ]);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Fetch failed",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid list ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "List item updated (mock)",
                "id" => $id
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
                throw new HttpBadRequestException($request, "Invalid list ID");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "List item deleted (mock)",
                "id" => $id
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
