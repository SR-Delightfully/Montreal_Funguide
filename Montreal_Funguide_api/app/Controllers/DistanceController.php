<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\API\DistanceCalculator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DistanceController extends BaseController
{
    private DistanceCalculator $calculator;

    public function __construct()
    {
        $this->calculator = new DistanceCalculator();
    }

    public function calculate(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $start = [
            'lat' => $data['start_lat'] ?? null,
            'lng' => $data['start_lng'] ?? null,
        ];

        $end = [
            'lat' => $data['end_lat'] ?? null,
            'lng' => $data['end_lng'] ?? null,
        ];

        $result = $this->calculator->getDistance($start, $end);

        return $this->renderJson($response, $result);
    }
}
