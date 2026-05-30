<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\API\UnitConverter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConversionController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $type = $params['type'] ?? null;
        $ingredient = $params['ingredient'] ?? '';
        $value = (int) ($params['value'] ?? 0);
        $from = $params['from'] ?? '';
        $to = $params['to'] ?? '';

        if ($type === 'mass_to_volume') {
            $result = UnitConverter::ConvertMassToVolume(
                $value,
                $ingredient,
                $from,
                $to
            );
        } else {
            $result = UnitConverter::ConvertVolumeToMass(
                $value,
                $ingredient,
                $from,
                $to
            );
        }

        return $this->renderJson($response, [
            "result" => $result
        ]);
    }
}
