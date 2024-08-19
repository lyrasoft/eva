<?php

declare(strict_types=1);

namespace App\Formkit\Exception;

class FormkitUnpublishedException extends \UnexpectedValueException
{
    public function __construct(string $message = '', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
