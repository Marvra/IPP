<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\InvalidSourceException;

class Arguments
{
    private mixed $argsArray = [];
    public function __construct($instructionArgs) {
        $this->getArgs($instructionArgs);
    }

    public function getArgAtIndex(int $index) : array {
        if (isset($this->argsArray[$index])) {
            return $this->argsArray[$index];
        } else {
            throw new InvalidSourceException;
        }
    }

    public function getArgs(mixed $instructionArgs) : void {
        for ($i=1; $i <= $instructionArgs->childElementCount; $i++)
        {
            $getArgs = $instructionArgs->GetElementsByTagName("arg$i")->item(0);
            
            if ($getArgs === null) {
                throw new InvalidSourceException;
            }

            $type = $getArgs->getAttribute('type');

            if ($type == 'int') {
                $this->argsArray[] = array('type' => $type, 'value' => (int)$getArgs->nodeValue, 'valueType' => $type);
            } 
            else if ($type == 'string') {
                $this->argsArray[] = array('type'=> $type, 'value' => (string)$getArgs->nodeValue, 'valueType' => $type);
            }
            else if ($type == 'bool') {
                if ($getArgs->nodeValue === 'true')
                {
                    $this->argsArray[] = array('type'=> $type, 'value' => true, 'valueType' => $type);
                } 
                else
                {
                    $this->argsArray[] = array('type'=> $type, 'value' => false, 'valueType' => $type);
                }
            }
            else 
            {
                $this->argsArray[] = array('type' => $type, 'value' => $getArgs->nodeValue, 'valueType' => $type);
            }
        }
    }
}
