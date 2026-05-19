<?php

namespace App\Controllers\Recipe;

use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RecipeController extends BaseController
{
    public function index(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["recipes" => []]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->renderJson($response, ["message" => "Create recipe"]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["recipe_id" => $args['id']]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Update recipe"]);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        return $this->renderJson($response, ["message" => "Delete recipe"]);
    }
}