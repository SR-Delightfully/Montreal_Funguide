<?php

declare(strict_types=1);

namespace App\Controllers\Fungi;

use App\Controllers\BaseController;
use App\Domain\Models\Fungi\HabitatModel as FungiHabitatModel;
use App\Domain\Models\HabitatModel;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

class HabitatController extends BaseController
{
    public function __construct(
        private FungiHabitatModel $habitatModel
    ) {}

    public function index(Request $request, Response $response): Response
    {
        try {
            $data = $this->habitatModel->getAllHabitats();

            return $this->renderJson($response, [
                "status" => "success",
                "data" => $data
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch habitats",
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

            $data = $this->habitatModel->getHabitatsByFungi($fungiId);

            return $this->renderJson($response, [
                "status" => "success",
                "fungi_id" => $fungiId,
                "data" => $data
            ]);
        } catch (Exception $e) {
            return $this->renderJson($response, [
                "status" => "error",
                "message" => "Failed to fetch habitats",
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
