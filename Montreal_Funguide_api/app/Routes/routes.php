<?php

declare(strict_types=1);

use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Controllers\AboutController;
use App\Controllers\AuthController;
use App\Controllers\UserController;

use App\Controllers\Fungi\FungiController;
use App\Controllers\Fungi\SpeciesController;
use App\Controllers\Fungi\HabitatController;

use App\Controllers\Map\MapController;
use App\Controllers\Map\LocationController;
use App\Controllers\Map\TripController;

use App\Controllers\Recipe\RecipeController;
use App\Controllers\Recipe\IngredientController;
use App\Controllers\Recipe\CalorieController;

use App\Controllers\ConversionController;
use App\Controllers\DistanceController;

use App\Helpers\DateTimeHelper;

return static function (App $app): void {

    $app->get('/', [AboutController::class, 'handleAboutWebService']);

    $app->get('/ping', function (Request $request, Response $response): Response {
        $payload = [
            "status" => "ok",
            "timestamp" => DateTimeHelper::now(DateTimeHelper::Y_M_D_H_M),
        ];

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    });

    $app->group('/auth', function ($auth) {
        $auth->post('/register', [AuthController::class, 'register']);
        $auth->post('/login', [AuthController::class, 'login']);
    });

    $app->group('/users', function ($users) {
        $users->get('/users', [UserController::class, 'index']);
        $users->get('/users/{id}', [UserController::class, 'show']);
        $users->post('/users', [UserController::class, 'create']);
        $users->put('/users/{id}', [UserController::class, 'update']);
        $users->delete('/users/{id}', [UserController::class, 'delete']);
    });

    $app->group('/fungi', function ($fungi) {
        $fungi->get('', [FungiController::class, 'index']);
        $fungi->post('', [FungiController::class, 'create']);

        $fungi->get('/{id}', [FungiController::class, 'show']);
        $fungi->put('/{id}', [FungiController::class, 'update']);
        $fungi->delete('/{id}', [FungiController::class, 'delete']);

        $fungi->get('/{id}/species', [SpeciesController::class, 'showByFungi']);
        $fungi->get('/{id}/habitat', [HabitatController::class, 'showByFungi']);
    });

    $app->group('/recipes', function ($recipes) {
        $recipes->get('', [RecipeController::class, 'index']);
        $recipes->post('', [RecipeController::class, 'create']);

        $recipes->get('/{id:\d+}', [RecipeController::class, 'show']);
        $recipes->put('/{id:\d+}', [RecipeController::class, 'update']);
        $recipes->delete('/{id:\d+}', [RecipeController::class, 'delete']);

        $recipes->group('/ingredients', function ($ingredients) {
            $ingredients->get('', [IngredientController::class, 'index']);
            $ingredients->post('', [IngredientController::class, 'create']);
            $ingredients->get('/{id:\d+}', [IngredientController::class, 'show']);
            $ingredients->put('/{id:\d+}', [IngredientController::class, 'update']);
            $ingredients->delete('/{id:\d+}', [IngredientController::class, 'delete']);
        });

        $recipes->group('/calories', function ($calories) {
            $calories->get('', [CalorieController::class, 'index']);
            $calories->post('', [CalorieController::class, 'create']);
            $calories->get('/{id:\d+}', [CalorieController::class, 'show']);
        });
    });

    $app->group('/map', function ($map) {
        $map->get('', [MapController::class, 'index']);

        $map->group('/locations', function ($locations) {
            $locations->get('', [LocationController::class, 'index']);
            $locations->post('', [LocationController::class, 'create']);
            $locations->get('/{id:\d+}', [LocationController::class, 'show']);
            $locations->put('/{id:\d+}', [LocationController::class, 'update']);
            $locations->delete('/{id:\d+}', [LocationController::class, 'delete']);
        });

        $map->group('/trips', function ($trips) {
            $trips->get('', [TripController::class, 'index']);
            $trips->post('', [TripController::class, 'create']);
            $trips->get('/{id:\d+}', [TripController::class, 'show']);
            $trips->put('/{id:\d+}', [TripController::class, 'update']);
            $trips->delete('/{id:\d+}', [TripController::class, 'delete']);
        });
    });

    $app->group('/tools', function ($tools) {
        $tools->get('/convert', [ConversionController::class, 'index']);
        $tools->get('/distance', [DistanceController::class, 'calculate']);
    });
};
