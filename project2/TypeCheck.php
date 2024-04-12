<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\OperandTypeException;

class TypeCheck
{
    public static function checkInt($value): void
    {
        if (!is_int($value)) {
            throw new OperandTypeException;
        }
    }

    public static function checkString($value): void
    {
        if (!is_string($value)) {
            throw new OperandTypeException;
        }
    }

    public static function checkBool($value): void
    {
        if (!is_bool($value)) {
            throw new OperandTypeException;
        }
    }

    public static function equalType($value1, $value2): void
    {
        if (gettype($value1) !== gettype($value2)) {
            throw new OperandTypeException;
        }
    }
}