<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ConversionController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "Convert tool"]);
    }
}