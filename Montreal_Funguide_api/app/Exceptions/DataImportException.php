<?php 

declare(strict_types=1);

namespace App\Exceptions;
use Slim\Exception\HttpSpecializedException;
use Throwable;

class DataImportException extends HttpSpecializedException
{
    protected $code = 502;
    protected string $title = 'External Data Import Error';
    protected string $description = '[SERVER ERROR] A third-party API failed during data import.';
    protected string $source = 'unknown';

    public function __construct(
        $request = null,
        string $message = 'Data import failed.',
        int $code = 502,
        ?Throwable $previous = null
    ) {
        parent::__construct($request, $message, $code, $previous);
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
