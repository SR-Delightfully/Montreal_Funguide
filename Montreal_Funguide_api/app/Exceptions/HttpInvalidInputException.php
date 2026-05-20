<?php

namespace App\Exceptions;

use Slim\Exception\HttpSpecializedException;

class HttpInvalidInputException extends HttpSpecializedException
{
    protected $code = 400;
    protected string $title = 'Bad Request';
    protected string $message = '[ERROR] The request received contains invalid entries. please try again.';
}
