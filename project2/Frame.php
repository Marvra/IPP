<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\VariableAccessException;
use IPP\Student\ExceptionExtended\SemanticErrorException;
use IPP\Core\ReturnCode;
use Exception;

/**
 * Input reader that reads from a file
 */
class Frame
{
    public $globalFrame = [];
    public $localFrame = [];
    public $temporaryFrame = [];

    public function &getFrameVisibility($argValue): array 
    {
        $typeFrame = substr($argValue, 0, 2);
        switch($typeFrame)
        {
            case "GF":
                return $this->globalFrame;
            case "LF":
                return $this->localFrame;
            case "TF":
                return $this->temporaryFrame;
            default:
                throw new SemanticErrorException;
        }
    }

    public function defineVariable($varName) 
    {
        // Check if the variable already exists
        $table = &$this->getFrameVisibility($varName);
        if (array_key_exists($varName, $table)) {
            throw new SemanticErrorException;
        }
    
        $table[$varName] = null;
        // foreach ($table as $symb => &$val) {
        // echo " - Name: $symb, Value: ";
        // echo $val === null ? "null" : $val;
        // echo ", Type: " . gettype($val);
        // echo "\n";
        // }
        // echo "\n\n";
    
        return true;
    }
    
    public function assignValue($varName, $value)
    {
        // Check if the variable exists
        $table =& $this->getFrameVisibility($varName);
        
        // Echo the content of the frame
    
        if (!array_key_exists($varName, $table)) {
            throw new VariableAccessException;
        }
        if (is_array($value)) {
            if ($value['type'] == 'int' || is_int($value['value'])) {
                // echo "SOM INT KOKOT \n";
                $table[$varName] = (int)$value['value'];
            } else if ($value['type'] == 'string' || is_string($value['value'])) {
                // echo "SOM STRING KOKOT \n";
                $table[$varName] = (string)$value['value'];
            } else if ($value['type'] == 'bool' || is_bool($value['value'])) {
                // echo "SOM BOOL KOKOT \n";
                $table[$varName] = (bool)$value['value'];
            }
            else
            {
                // echo "SOM KOKOT \n";
                $table[$varName] = $value['value'];
            }
            // echo "SOM KOKOT \n";
            // $table[$varName] = $value['value'];
        } else {
            // echo "SOM\n";
            $table[$varName] = $value;
        }

        // foreach ($table as $symb => &$val) {
        // echo " - Name: $symb, Value: ";
        // echo $val === null ? "null" : $val;
        // echo ", Type: " . gettype($val);
        // echo "\n";
        // }
        // echo "\n\n";
        return true;
    }


    // Function to get the value of a variable
    public function getVariableValue($varName)
    {
        // Check if the variable exists
        $table = &$this->getFrameVisibility($varName);
        if (!array_key_exists($varName, $table)) {
            throw new VariableAccessException;
        }

        // Return the value of the variable
        return $table[$varName];
    }

}
