<?php
namespace IPP\Student\ExceptionExtended;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

class ValueErrorException extends IPPException
{
    public function __construct(string $message = "Value error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::VALUE_ERROR, $previous);
    }
}
