<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\InvalidSourceException;
use IPP\Core\ReturnCode;
use Exception;

/**
 * Input reader that reads from a file
 */
class Arguments
{
    private $argsArray = [];
    public function __construct($instructionArgs) {
        $this->getArgs($instructionArgs);
    }

    public function getArgAtIndex($index) {
        if (isset($this->argsArray[$index])) {
            return $this->argsArray[$index];
        } else {
            throw new InvalidSourceException;
        }
    }

    public function getArgs($instructionArgs) {
        if ($instructionArgs->childElementCount == 0) {
            throw new InvalidSourceException;
        }
        for ($i=1; $i <= $instructionArgs->childElementCount; $i++)
        {
            $getArgs = $instructionArgs->GetElementsByTagName("arg$i")->item(0);
            $type = $getArgs->getAttribute('type');
            if ($type == 'int') {
                $this->argsArray[] = array('type' => $type, 'value' => (int)$getArgs->nodeValue);
            } 
            else if ($type == 'string') {
                $this->argsArray[] = array('type'=> $type, 'value' => (string)$getArgs->nodeValue);
            }
            else if ($type == 'bool') {
                if ($getArgs->nodeValue === 'true')
                {
                    $this->argsArray[] = array('type'=> $type, 'value' => true );
                } 
                else
                {
                    $this->argsArray[] = array('type'=> $type, 'value' => false);
                }
            }
            else
            {
                $this->argsArray[] = array('type' => $type, 'value' => $getArgs->nodeValue);
            }  
                
            // $this->argsArray[] = array('type' => $type, 'value' => $getArgs->nodeValue);
        }
    }
}
