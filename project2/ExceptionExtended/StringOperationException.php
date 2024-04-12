<?php
namespace IPP\Student\ExceptionExtended;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

/**
 * Exception for ill-defined student classes
 */
class StringOperationException extends IPPException
{
    public function __construct(string $message = "String operation error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::STRING_OPERATION_ERROR, $previous);
    }
}
