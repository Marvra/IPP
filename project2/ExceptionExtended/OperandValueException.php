<?php
namespace IPP\Student\ExceptionExtended;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

/**
 * Exception for ill-defined student classes
 */
class OperandValueException extends IPPException
{
    public function __construct(string $message = "Operand value error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::OPERAND_VALUE_ERROR, $previous);
    }
}
