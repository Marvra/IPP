<?php
namespace IPP\Student;

use Exception;
use IPP\Core\AbstractInterpreter;
use IPP\Core\ReturnCode;
use IPP\Student\FileSort;
use IPP\Student\Lable;
use IPP\Student\Arguments;
use IPP\Student\Instruction;

class Interpreter extends AbstractInterpreter
{

    public function execute(): int
    {
        $dom = $this->source->getDOMDocument();
        $fileSort = new FileSort();
        $instruction = new Instruction($this->settings);
        
        try{
            $instructionArray = $fileSort->SortByOrder($dom);
            $lable = new Lable($instructionArray);
        }
        catch (Exception $e) 
        {
            echo "Error: " . $e->getMessage() . PHP_EOL;
            return $e->getCode();
        } 

        for ($i=0; $i < count($instructionArray); $i++)
        {
            $argsArray = New Arguments($instructionArray[$i]);

            try{
                $opcode = $instructionArray[$i]->getAttribute('opcode');
                $instruction->incrementPositionCounter();
                
                // echo "Instruction: $opcode\n";
                // echo "Position counter: $this->postionCounter\n";

                switch (strtoupper($opcode)) {
                    case 'DEFVAR':
                        // // $argsArray = $this->getArgs($instructionArray[$i]);
                        $instruction->DEFVAR($argsArray->getArgAtIndex(0)['value']);
                        break;
                    
                    case 'CREATEFRAME':
                        // // $argsArray = $this->getArgs($instructionArray[$i]);
                        $instruction->CREATEFRAME();
                        break;

                    case 'PUSHFRAME':
                        // // $argsArray = $this->getArgs($instructionArray[$i]);
                        $instruction->PUSHFRAME();
                        break;
                    
                    case 'POPFRAME':
                        // // $argsArray = $this->getArgs($instructionArray[$i]);
                        $instruction->PUSHFRAME();
                        break;

                    case 'MOVE':
                        // // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->MOVE($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1));
                        break;
                    
                    case 'ADD':
                        // // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->ADD($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'MUL':
                        // // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->MUL($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'SUB':
                        // // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->SUB($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'IDIV':
                        // // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->IDIV($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'AND':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->AND($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'OR':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->OR($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'NOT':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->NOT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1));
                        break;

                    case 'LT':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->LT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'GT':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->GT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'EQ':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->EQ($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;
                    
                    case 'TYPE':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->TYPE($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1));
                        break;
                    
                    case 'CONCAT':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->CONCAT($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;

                    case 'READ':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->READ($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1));
                        break;

                    case 'WRITE':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->WRITE($argsArray->getArgAtIndex(0));
                        break;

                    case 'DPRINT':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->DPRINT($argsArray->getArgAtIndex(0));
                        break;

                    case 'POPS':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->POPS($argsArray->getArgAtIndex(0));
                        break;

                    case 'PUSHS':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->PUSHS($argsArray->getArgAtIndex(0));
                        break;
                        
                    case 'INT2CHAR':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->INT2CHAR($argsArray->getArgAtIndex(0),$argsArray->getArgAtIndex(1));
                        break;
                    
                    case 'STRI2INT':
                        $instruction->STRI2INT($argsArray->getArgAtIndex(0),$argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;

                    case 'GETCHAR':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->GETCHAR($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;

                    case 'SETCHAR':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->SETCHAR($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2));
                        break;

                    case 'STRLEN':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $instruction->STRLEN($argsArray->getArgAtIndex(0), $argsArray->getArgAtIndex(1));
                        break;

                    case 'LABEL':
                        break;
                    
                    case 'JUMP':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $i = $instruction->JUMP($argsArray->getArgAtIndex(0)['value'], $lable) - 1;
                        $instruction->setPositionCounter($i);
                        break;

                    case 'JUMPIFEQ':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $i = $instruction->JUMPIFEQ($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2),  $lable) - 1;
                        break;
                    
                    case 'JUMPIFNEQ':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $i = $instruction->JUMPIFNEQ($argsArray->getArgAtIndex(0)['value'], $argsArray->getArgAtIndex(1), $argsArray->getArgAtIndex(2),  $lable) - 1;
                        break;

                    case 'CALL':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $i = $instruction->CALL($argsArray->getArgAtIndex(0)['value'], $lable) - 1;
                        $instruction->setPositionCounter($i);
                        break;
                    
                    case 'RETURN':
                        return ReturnCode::OK;

                    case 'EXIT':
                        // $argsArray = $instruction->getArgs($instructionArray[$i]);
                        $exit = $instruction->EXIT($argsArray->getArgAtIndex(0));
                        return $exit;
                    
                    default:
                        return ReturnCode::INVALID_SOURCE_STRUCTURE;
                }
            } 
            catch (Exception $e) 
            {
                echo "Error: " . $e->getMessage() . PHP_EOL;
                return $e->getCode();
            } 

            // foreach($this->symbolTable as $symb=>$value)
            // {
            //     echo " - Name: $symb, Value: ";
            //     echo $value === null ? "null" : $value;
            //     echo "\n";
            // }
            // echo"\n\n";//
        }

        return ReturnCode::OK;
    }


}
