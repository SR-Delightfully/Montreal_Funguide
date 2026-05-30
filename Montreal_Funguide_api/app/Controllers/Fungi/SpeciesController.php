<?php

declare(strict_types=1);

namespace App\Controllers\Fungi;

use App\Controllers\BaseController;
use App\Domain\Models\Fungi\SpeciesModel as FungiSpeciesModel;
use App\Domain\Models\SpeciesModel;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpNotFoundException;

class SpeciesController extends BaseController
{
    public function __construct(
        private FungiSpeciesModel $speciesModel
    ) {}

    public function index(Request $request, Response $response): Response
    {
        try {
            $data = $this->speciesModel->getAllSpecies();

            return $this->renderJson($response, [
                "status" => "success",
                "data" => $data
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch species",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        try {
            $id = (int)($args['id'] ?? 0);

            if ($id <= 0) {
                throw new HttpBadRequestException($request, "Invalid species ID");
            }

            $data = $this->speciesModel->getSpeciesById($id);

            if (!$data) {
                throw new HttpNotFoundException($request, "Species not found");
            }

            return $this->renderJson($response, [
                "status" => "success",
                "data" => $data
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch species",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function showByFungi(Request $request, Response $response, array $args): Response
    {
        try {
            $fungiId = (int)($args['id'] ?? 0);

            if ($fungiId <= 0) {
                throw new HttpBadRequestException($request, "Invalid fungi ID");
            }

            $data = $this->speciesModel->getSpeciesByFungi($fungiId);

            return $this->renderJson($response, [
                "status" => "success",
                "fungi_id" => $fungiId,
                "data" => $data
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch species",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
