<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DistanceController extends BaseController
{
    public function calculate(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["distance" => 0]);
    }
}