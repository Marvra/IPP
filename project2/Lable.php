<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\SemanticErrorException;
use IPP\Student\Arguments;
use IPP\Core\ReturnCode;
use Exception;

/**
 * Input reader that reads from a file
 */
class Lable
{
    public $lableTable = [];
    public function __construct($instructionArray)
    {
        $this->SetLables($instructionArray);
    }

    public function SetLables($sortedFile){
        $postion = 0;
        foreach($sortedFile as $instruction){
            $postion++;
            $opcode = $instruction->getAttribute('opcode');
            if($opcode == 'LABEL'){

                $argsArray = New Arguments($instruction);
                $arg = $argsArray->getArgAtIndex(0);

                if($arg['type'] != 'label'){
                    throw new SemanticErrorException;
                }
                if (array_key_exists($arg['value'], $this->lableTable)) {
                    throw new SemanticErrorException;
                }
                
                $this->lableTable[$arg['value']] = $postion;
            }
        }
    }

    public function getPositionCounterForLabel($labelName) {
        if (isset($this->lableTable[$labelName])) {
            return $this->lableTable[$labelName];
        } else {
            throw new SemanticErrorException;
        }
    }

}
