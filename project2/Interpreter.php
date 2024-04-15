<?php
namespace IPP\Student;

use IPP\Core\AbstractInterpreter;
use IPP\Core\ReturnCode;
use IPP\Student\FileSort;
use IPP\Student\Lable;
use IPP\Student\Arguments;
use IPP\Student\Instruction;

use IPP\Student\ExceptionExtended\InvalidSourceException;

class Interpreter extends AbstractInterpreter
{

    public function execute(): int
    {
        $dom = $this->source->getDOMDocument();
        // creating instance of instruction class
        $instruction = new Instruction($this->settings);
        // sorting file by order saving it as an array
        $instructionArray = FileSort::SortByOrder($dom);
        // getting lables and theyr positions from sorted file
        $lable = new Lable($instructionArray);

        // parsing all instructions in instruction array
        for ($i=0; $i < count($instructionArray);)
        {
            // getting arguments of the instruction
            $argsArray = New Arguments($instructionArray[$i]);
            // getting name of the instruction
            $opcode    = $instructionArray[$i]->getAttribute('opcode');
            // echo '--------------------------------------------------------------' . PHP_EOL;
            // echo "Position counter: $instruction->positionCounter\n";
            // echo "Instruction: $opcode\n";

            // switch for various instructions (strtoupper beacause of case insensitivity)
            switch (strtoupper($opcode)) {
                // filling arguments by documentation
                //  -each function has specified amount of arguments 
                case 'DEFVAR': 
                    $instruction->DEFVAR($argsArray->getArgAtIndex(0)['value']);
                    break;
                
                case 'CREATEFRAME': 
                    $instruction->CREATEFRAME();
                    break;

                case 'PUSHFRAME': 
                    $instruction->PUSHFRAME();
                    break;
                
                case 'POPFRAME': 
                    $instruction->POPFRAME();
                    break;

                case 'MOVE': 
                    $instruction->MOVE($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1));
                    break;
                
                case 'ADD': 
                    $instruction->ADD($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'MUL': 
                    $instruction->MUL($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'SUB': 
                    $instruction->SUB($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'IDIV': 
                    $instruction->IDIV($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'AND': 
                    $instruction->AND($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'OR': 
                    $instruction->OR($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'NOT': 
                    $instruction->NOT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1));
                    break;

                case 'LT': 
                    $instruction->LT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'GT': 
                    $instruction->GT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'EQ': 
                    $instruction->EQ($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;
                
                case 'TYPE': 
                    $instruction->TYPE($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1));
                    break;
                
                case 'CONCAT': 
                    $instruction->CONCAT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;

                case 'READ': 
                    $instruction->READ($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1));
                    break;

                case 'WRITE': 
                    $instruction->WRITE($argsArray->getArgAtIndex(0));
                    break;

                case 'DPRINT': 
                    $instruction->DPRINT($argsArray->getArgAtIndex(0));
                    break;

                case 'POPS': 
                    $instruction->POPS($argsArray->getArgAtIndex(0));
                    break;

                case 'PUSHS': 
                    $instruction->PUSHS($argsArray->getArgAtIndex(0));
                    break;
                    
                case 'INT2CHAR': 
                    $instruction->INT2CHAR($argsArray->getArgAtIndex(0),$argsArray->getArgAtIndex(1));
                    break;
                
                case 'STRI2INT': 
                    $instruction->STRI2INT($argsArray->getArgAtIndex(0),$argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;

                case 'GETCHAR': 
                    $instruction->GETCHAR($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;

                case 'SETCHAR': 
                    $instruction->SETCHAR($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                    break;

                case 'STRLEN': 
                    $instruction->STRLEN($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1));
                    break;

                case 'LABEL': 
                    break;
                
                case 'JUMP': 
                    $i = $instruction->JUMP($argsArray->getArgAtIndex(0)['value'], $lable);
                    break;

                case 'JUMPIFEQ': 
                    $i = $instruction->JUMPIFEQ($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2),  $lable);
                    break;
                
                case 'JUMPIFNEQ': 
                    $i = $instruction->JUMPIFNEQ($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2),  $lable);
                    break;

                case 'CALL': 
                    $i = $instruction->CALL($argsArray->getArgAtIndex(0)['value'], $lable);
                    break;
                
                case 'RETURN': 
                    $i = $instruction->RETURN();
                    break;

                case 'EXIT': 
                    $exit = $instruction->EXIT($argsArray->getArgAtIndex(0));
                    return $exit;
                
                default: 
                    throw new InvalidSourceException;
            }
            // echo PHP_EOL;
            // $instruction->table->printFrames();
            // print_r("FRAME STACK : ");
            // print_r($instruction->table->frameStack);
            // echo PHP_EOL;
            
            $i++;
            $instruction->position->incrementPositionCounter();
        }
        return ReturnCode::OK;
    }
}
