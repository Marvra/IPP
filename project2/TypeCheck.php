<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\OperandTypeException;

class TypeCheck
{
    /*
    *   Method which checks if type is int otherwise throws OperandException
    */
    public static function checkInt(mixed $value): void
    {
        if ($value != 'int') {
            throw new OperandTypeException;
        }
    }

    /*
    *   Method which checks if type is string otherwise throws OperandException
    */
    public static function checkString(mixed $value): void
    {
        if ($value != 'string') {
            throw new OperandTypeException;
        }
    }

    /*
    *   Method which checks if type is bool otherwise throws OperandException
    */
    public static function checkBool(mixed $value): void
    {
        if ($value != 'bool') {
            throw new OperandTypeException;
        }
    }

    /*
    *   Method which checks if type is nil otherwise throws OperandException
    */
    public static function checkIsNil(mixed $value): void
    {
        if ($value == 'nil') {
            throw new OperandTypeException;
        }
    }

    /*
    *   Method which checks if types are equal otherwise throws OperandException
    */
    public static function checkEqualType(mixed $value1, mixed $value2): void
    {
        if ($value1 !== $value2) {
            throw new OperandTypeException;
        }
    }

    /*
    *   Method which checks if types are equal and arent type nil (EQ instruction requierements) otherwise throws OperandException
    */
    public static function checkEqualTypeForEQ(mixed $value1, mixed $value2): void
    {
        if ($value1 !== $value2 && $value1 != 'nil' && $value2 != 'nil') {
            throw new OperandTypeException;
        }
    }
}