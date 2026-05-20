<?php

declare(strict_types=1);

use App\Middleware\HelloMiddleware;
use Slim\App;

return function (App $app) {
    // TODO: add middleware to handle CORS & security headers
    // TODO: add middleware for API versioning
    // TODO: add middleware for Auth validation
    // TODO: add middleware for pagination validation
    // TODO: add middleware for logging API interactions
    // TODO: add middleware for preventing excessive use
    // TODO: add middleware for URI Sanatization and Validation

    $app->addBodyParsingMiddleware();
    $app->addRoutingMiddleware();

    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorMiddleware->getDefaultErrorHandler()->forceContentType(APP_MEDIA_TYPE_JSON);
};
