<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\VariableAccessException;
use IPP\Student\ExceptionExtended\FrameAccessException;
use IPP\Student\ExceptionExtended\SemanticErrorException;

class Frame
{
    public $globalFrame = [];
    public $localFrame = null;
    public $temporaryFrame = null;

    public $frameStack = [];

    public function &getFrameVisibility(string $argValue) : array 
    {
        $typeFrame = substr($argValue, 0, 2);
        switch($typeFrame)
        {
            case "GF":
                return $this->globalFrame;
            case "LF":
                return $this->localFrame;
                if ($this->temporaryFrame === null)
                {
                    throw new FrameAccessException;
                }
            case "TF":
                if ($this->temporaryFrame === null)
                {
                    throw new FrameAccessException;
                }
                return $this->temporaryFrame;
            default:
                throw new SemanticErrorException;
        }
    }

    public function defineVariable(string $varName): void
    {
        $table = &$this->getFrameVisibility($varName);
        if (array_key_exists($varName, $table)) {
            throw new SemanticErrorException;
        }
    
        $table[$varName] = array('value' => null, 'valueType' => null);
    }
    
    public function assignValue(string $varName, mixed $value) : void
    {
        $table =& $this->getFrameVisibility($varName);
        
        if (!array_key_exists($varName, $table)) {
            throw new VariableAccessException;
        }
        if (is_array($value)) {
            switch ($value['valueType']) {
                case 'int':
                    $table[$varName]['value'] = (int)$value['value'];
                    $table[$varName]['valueType'] = 'int';
                    break;
                case 'string':
                    $table[$varName]['value'] = (string)$value['value'];
                    $table[$varName]['valueType'] = 'string';
                    break;
                case 'bool':
                    $table[$varName]['value'] = (bool)$value['value'];
                    $table[$varName]['valueType'] = 'bool';
                    break;
                case 'nil':
                    $table[$varName]['value'] = $value['value'];
                    $table[$varName]['valueType'] = 'nil';
                    break;
                case 'var':
                    $table[$varName]['value'] = $value['value'];
                    $table[$varName]['valueType'] = 'var';
                    break;
                default:
                    throw new SemanticErrorException;
                }
            
        } else {
            switch (gettype($value)) {
                case 'integer':
                    $table[$varName]['value'] = (int)$value;
                    $table[$varName]['valueType'] = 'int';
                    break;
                case 'string':
                    $table[$varName]['value'] = (string)$value;
                    $table[$varName]['valueType'] = 'string';
                    break;
                case 'boolean':
                    $table[$varName]['value'] = (bool)$value;
                    $table[$varName]['valueType'] = 'bool';
                    break;
                default:
                    $table[$varName]['value'] = $value;
                    $table[$varName]['valueType'] = 'nil';
                    break;
                }
        }
    }

    public function getVariable(string $varName) : array
    {
        // Check if the variable exists
        $table = &$this->getFrameVisibility($varName);
        if (!array_key_exists($varName, $table)) {
            throw new VariableAccessException;
        }

        // Return the value of the variable
        return $table[$varName];
    }
    // Function to get the value of a variable
    public function getVariableValue(string $varName) : mixed
    {
        // Check if the variable exists
        $table = &$this->getFrameVisibility($varName);
        if (!array_key_exists($varName, $table)) {
            throw new VariableAccessException;
        }

        // Return the value of the variable
        return $table[$varName]['value'];
    }

    public function getVariableValueType(string $varName) : string|null
    {
        // Check if the variable exists
        $table = &$this->getFrameVisibility($varName);
        if (!array_key_exists($varName, $table)) {
            throw new VariableAccessException;
        }

        // Return the value of the variable
        return $table[$varName]['valueType'];
    }

    public function printFrames() : void
    {
        echo "Global Frame: \n";
        foreach ($this->globalFrame as $symb => &$val) {
        echo " - Name: $symb, Value: ";
        echo $val === null ? "null" : $val;
        echo ", Type: " . gettype($val);
        echo "\n";
        }
        echo "Local Frame: \n";
        foreach ($this->localFrame as $symb => &$val) {
        echo " - Name: $symb, Value: ";
        echo $val === null ? "null" : $val;
        echo ", Type: " . gettype($val);
        echo "\n";
        }
        echo "Temporary Frame: \n";
        if ($this->temporaryFrame === null) 
        {
            echo "null\n";
        }
        else
        {
            foreach ($this->temporaryFrame as $symb => &$val) {
            echo " - Name: $symb, Value: ";
            echo $val === null ? "null" : $val;
            echo ", Type: " . gettype($val);
            echo "\n";
            }
        }

    }

}
