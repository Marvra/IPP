<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\SemanticErrorException;
use IPP\Student\Arguments;

class Lable
{
    public array $lableTable = [];

    public function __construct(array $instructionArray)
    {
        // calling SetLables which fills out lableTable with lables and their positions
        $this->SetLables($instructionArray);
    }

    /*
    *   Method for filling lable table with lable names and their positions
    */
    public function SetLables(array $sortedFile) : void {
        $postion = 0;

        // parsing through array looking for lables
        foreach($sortedFile as $instruction){
            $opcode = $instruction->getAttribute('opcode');
            if($opcode == 'LABEL'){

                // getting theyr arguemtns (which should only be their name)
                $argsArray = New Arguments($instruction);
                $arg = $argsArray->getArgAtIndex(0);

                // only type lable should be used for lables
                if($arg['type'] != 'label'){
                    throw new SemanticErrorException;
                }
                // checking if lable doesnt already exists within lableTable
                if (array_key_exists($arg['value'], $this->lableTable)) {
                    throw new SemanticErrorException;
                }
                
                // getting position of lable 
                $this->lableTable[$arg['value']] = $postion;
            }
            $postion++;
        }
    }

    /*
    *   Method for getting position of specific lable
    */
    public function getPositionCounterForLabel(string $labelName) : int {
        // checking if the lable with that name isnt already set 
        if (isset($this->lableTable[$labelName])) {
            // getting position of given lable
            return $this->lableTable[$labelName];
        } else {
            throw new SemanticErrorException;
        }
    }

}
