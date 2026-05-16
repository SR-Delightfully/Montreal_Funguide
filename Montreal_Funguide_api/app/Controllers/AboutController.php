<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AboutController extends BaseController
{
    private const API_NAME = 'YOUR_PROJECT_NAME';

    private const API_VERSION = '1.0.0';

    // TODO: Create Auth Controller 
    // - enamble user registration and authorization
    // TODO: Create Fungi Controller
    // - fungi class
    // - (fungi)habitat class
    // - (fungi)species class
    // TODO: Create Recipe Controller
    // - recipe class
    // - ingredient class
    // - calorie class
    // TODO: Create Map Controller
    // - map class
    // - location class
    // - trip class

    public function handleAboutWebService(Request $request, Response $response): Response
    {

        $data = array(
            'api' => self::API_NAME,
            'version' => self::API_VERSION,
            'about' => 'Welcome to the MontrealFunguide! A delightful Web service that provides various informaion on fungi local to Montreal, where you can find them, and what you can cook up with them!',
            'authors' => 'SR-Delightfully',
            'resources' => ['/species', '/recipes', '/map'],
            'resource_details' => ['/species/{fungi_id}', '/recipes/{recipe_id}', '/map/{location_id}'],
            'example_queries' => [
                '/species/{fungi_id}/recipes',
                '/map?fungi_id={fungi_id}'
            ],
        );

        return $this->renderJson($response, $data);
    }
}
