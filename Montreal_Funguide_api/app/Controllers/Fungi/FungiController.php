<?php

declare(strict_types=1);

namespace App\Controllers\Fungi;

use App\Controllers\BaseController;
use App\Domain\Models\Fungi\FungiModel as FungiFungiModel;
use App\Domain\Models\FungiModel;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class FungiController extends BaseController
{
    public function __construct(
        private FungiFungiModel $fungiModel
    ) {}

    public function index(Request $request, Response $response): Response
    {
        try {
            $data = $this->fungiModel->getAllFungi();

            return $this->renderJson($response, [
                "status" => "success",
                "data" => $data
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch fungi",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            if (!$data) {
                throw new HttpBadRequestException($request, "Invalid request body");
            }

            $id = $this->fungiModel->createFungi($data);

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Fungi created",
                "id" => $id
            ], 201);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to create fungi",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid fungi ID");
            }

            $fungi = $this->fungiModel->getFungiById($id);

            if (!$fungi) {
                throw new HttpNotFoundException($request, "Fungi not found");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "data" => $fungi
            ]);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);
        } catch (HttpNotFoundException $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => $e->getMessage()
            ], 404);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch fungi",
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
                throw new HttpBadRequestException($request, "Invalid fungi ID");
            }

            $updated = $this->fungiModel->updateFungi($id, $data);

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Fungi updated",
                "updated" => $updated
            ]);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to update fungi",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid fungi ID");
            }

            $this->fungiModel->deleteFungi($id);

            return $this->renderJson($response, [
                "status" => "success",
                "message" => "Fungi deleted"
            ]);
        } catch (HttpBadRequestException $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => $e->getMessage()
            ], 400);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to delete fungi",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
