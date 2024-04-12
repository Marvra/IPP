<?php
namespace IPP\Student\ExceptionExtended;

use IPP\Core\Exception\IPPException;
use IPP\Core\ReturnCode;
use Throwable;

/**
 * Exception for ill-defined student classes
 */
class SemanticErrorException extends IPPException
{
    public function __construct(string $message = "Semantic error", ?Throwable $previous = null)
    {
        parent::__construct($message, ReturnCode::SEMANTIC_ERROR, $previous);
    }
}
