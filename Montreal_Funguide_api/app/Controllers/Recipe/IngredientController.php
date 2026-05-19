<?php

namespace App\Controllers\Recipe;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IngredientController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["ingredients" => []]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "Create ingredient"]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["ingredient_id" => $args['id']]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Update ingredient"]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Delete ingredient"]);
    }
}