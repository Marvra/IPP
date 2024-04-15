<?php
namespace IPP\Student;

use IPP\Core\Interface\InputReader;
use IPP\Core\Interface\OutputWriter;
use IPP\Core\Settings;
use IPP\Student\TypeCheck;
use IPP\Student\Frame;
use IPP\Student\Resolve;

use IPP\Student\ExceptionExtended\FrameAccessException;
use IPP\Student\ExceptionExtended\OperandTypeException;
use IPP\Student\ExceptionExtended\OperandValueException;
use IPP\Student\ExceptionExtended\SemanticErrorException;
use IPP\Student\ExceptionExtended\StringOperationException;
use IPP\Student\ExceptionExtended\ValueErrorException;

class Instruction
{
    public Position $position;
    public Resolve $resolve;
    public Frame $table;
    public InputReader $readerIn;
    public OutputWriter $stdout;
    public OutputWriter $stderr;

    public array $stack = [];

    /**
     * @var array<int>
     */
    public array $callPosition = [];


    public function __construct(Settings $settings) {
        $this->table = new Frame();
        $this->resolve = new Resolve($this->table);
        $this->position = new Position();
        $this->readerIn = $settings->getInputReader();
        $this->stdout = $settings->getStdOutWriter();
        $this->stderr = $settings->getStdErrWriter();
    }

    public function JUMP(string $labelName, Lable $lableTable) : int {
        $psCounter = $lableTable->getPositionCounterForLabel($labelName)-1;
        $this->position->setPositionCounter($psCounter);
        return $psCounter;
    }

    public function CALL(string $labelName, Lable $lableTable) : int{
        array_push($this->callPosition, $this->position->positionCounter);
        $psCounter = $lableTable->getPositionCounterForLabel($labelName)-1;
        $this->position->setPositionCounter($psCounter);
        return $psCounter;
    }

    public function RETURN() : int{
        if (empty($this->callPosition)) {
            throw new ValueErrorException;
        }
        $psCounter = array_pop($this->callPosition);
        $this->position->setPositionCounter($psCounter);
        return $psCounter;
    }

    public function JUMPIFEQ(string $labelName, mixed $symb1, mixed $symb2, Lable $lableTable) : int{
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        

        TypeCheck::checkEqualTypeForEQ($this->resolve->resolveSymbolType($symb1), $this->resolve->resolveSymbolType($symb2));

        if ($comp1 == $comp2) {
            $count = $lableTable->getPositionCounterForLabel($labelName)-1;
            $this->position->setPositionCounter($count);
            return $count;
        } else {
            return $this->position->getPositionCounter();
        }
    }

    public function JUMPIFNEQ(string $labelName, mixed $symb1, mixed $symb2, Lable $lableTable) : int {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkEqualTypeForEQ($this->resolve->resolveSymbolType($symb1), $this->resolve->resolveSymbolType($symb2));
        
        if ($comp1 != $comp2) {
            $count = $lableTable->getPositionCounterForLabel($labelName)-1;
            $this->position->setPositionCounter($count);
            return $count;
        } else {
            return $this->position->getPositionCounter();
        }
    }

    public function DEFVAR(string $varName) : void {
        $this->table->defineVariable($varName);

    }

    public function ADD(string $varName, mixed $symb1, mixed $symb2) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb2));

        $value = $comp1 + $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function MUL(string $varName, mixed $symb1, mixed $symb2) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb2));

        $value = $comp1 * $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function SUB(string $varName, mixed $symb1, mixed $symb2) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb2));

        $value = $comp1 - $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function IDIV(string $varName, mixed $symb1, mixed $symb2) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);
    
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb2));

        if ($comp2 == 0) {
            throw new OperandValueException;
        }
    
        $value = $comp1 / $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function STRLEN(mixed $varName, mixed $symb1) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        
        if ($symb1['valueType'] == 'nil') {
            throw new OperandTypeException;
        }

        TypeCheck::checkString($this->resolve->resolveSymbolType($symb1));

        $this->table->assignValue($varName['value'], (int)strlen($comp1));
    }

    public function CREATEFRAME() : void {
       $this->table->temporaryFrame = [];
    }

    public function PUSHFRAME() : void {
        $this->table->localFrame = [];

        if ($this->table->temporaryFrame === null)
        {
            throw new FrameAccessException;
        }
        // Push the temporary frame onto the stack
        if (!empty($this->table->temporaryFrame))
        {
             // Iterate through the temporary frame
            foreach ($this->table->temporaryFrame as $key => $value) 
            {

                // Change the key from "TF" to "LF"
                $modifiedKey = 'LF' . substr($key, 2);
                // Assign the modified key and value to the local frame
                $this->table->localFrame[$modifiedKey] = $value;
                // array_shift($this->table->localFrame);
            }
        }
        array_push($this->table->frameStack, $this->table->localFrame);
        // array_pop($this->table->localFrame);
        
        // $this->table->localFrame = [];
        // Clear the temporary frame
        $this->table->temporaryFrame = null;
    }

    public function POPFRAME() : void {

        if (empty($this->table->frameStack)) {
            throw new FrameAccessException;
        }

        $topFrame = array_pop($this->table->frameStack);
        foreach ($topFrame as $key => $value) {
            // Modify the key from "LF" to "TF"
            $modifiedKey = 'TF' . substr($key, 2);
            // Assign the modified key and value to the temporary frame
            $this->table->temporaryFrame[$modifiedKey] = $value;
        }

        if (!empty($this->table->frameStack)) {
            $topFrame = $this->table->frameStack[count($this->table->frameStack)-1];
            foreach ($topFrame as $key => $value) {
                // Modify the key from "LF" to "TF"
                $modifiedKey = 'LF' . substr($key, 2);
                // Assign the modified key and value to the temporary frame
                $this->table->localFrame[$modifiedKey] = $value;
            }

            // array_push($this->table->localFrame, $this->table->frameStack[count($this->table->frameStack)-1]);
        } 
        else {
            $topFrame = [];
            foreach($this->table->localFrame as $key => $value) {
                $modifiedKey = 'TF' . substr($key, 2);
                $topFrame[$modifiedKey] = $value;
            }
            $this->table->temporaryFrame = $topFrame;
        }
    }

    public function MOVE(mixed $varName, mixed $value) : void {

        $value = $this->resolve->resolveValue($value);

        $this->table->assignValue($varName['value'], $value);
    }


    public function AND(string $varName, mixed $symb1, mixed $symb2) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkBool($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkBool($this->resolve->resolveSymbolType($symb2));

        $value = $comp1 && $comp2  ? true : false;

        $this->table->assignValue($varName, $value);
    }
    
    public function OR(string $varName, mixed $symb1, mixed $symb2) : void  {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkBool($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkBool($this->resolve->resolveSymbolType($symb2));

        $value = $comp1 || $comp2  ? true : false;

        $this->table->assignValue($varName, $value);
    }
    
    public function NOT(string $varName, mixed $symb1) : void  {
        $comp1 = $this->resolve->resolveSymbol($symb1);

        TypeCheck::checkBool($this->resolve->resolveSymbolType($symb1));

        $value = !$comp1 ? true : false;

        $this->table->assignValue($varName, $value);
    }

    public function LT(string $varName, mixed $symb1, mixed $symb2) : void  {

        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        $symbType1 = $this->resolve->resolveSymbolType($symb1);
        $symbType2 = $this->resolve->resolveSymbolType($symb2);
        TypeCheck::checkIsNil($symbType1);
        TypeCheck::checkIsNil($symbType2);
        TypeCheck::checkEqualType($symbType1, $symbType2);

    
        $value = ($comp1 < $comp2) ? true : false;
        $this->table->assignValue($varName, $value);
    }
    
    public function GT(string $varName, mixed $symb1, mixed $symb2) : void   {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        $symbType1 = $this->resolve->resolveSymbolType($symb1);
        $symbType2 = $this->resolve->resolveSymbolType($symb2);
        TypeCheck::checkIsNil($symbType1);
        TypeCheck::checkIsNil($symbType2);
        TypeCheck::checkEqualType($symbType1, $symbType2);

        $value = ($comp1 > $comp2) ? true : false;
        $this->table->assignValue($varName, $value);
    }
    
    public function EQ(string $varName, mixed $symb1, mixed $symb2) : void   {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);
    
        // Check if the types are the same

        TypeCheck::checkEqualTypeForEQ($this->resolve->resolveSymbolType($symb1), $this->resolve->resolveSymbolType($symb2));
    
        $value = ($comp1 === $comp2) ? true : false;
        $this->table->assignValue($varName, $value);
        
    }

    public function TYPE(mixed $varName, mixed $symb1) : void {
        $comp1 = $this->resolve->resolveSymbolType($symb1);
        $value = '';
        switch ($comp1)
        {
            case 'int':
                $value = 'int';
                break;
            case 'string':
                $value = 'string';
                break;
            case 'bool':
                $value = 'bool';
                break;
            case 'nil':
                $value = 'nil';
                break;
            default:
                break;
        }
        $this->table->assignValue($varName['value'], $value);
        
    }

    public function CONCAT(string $varName, mixed $symb1, mixed $symb2) : void {
        if ($symb1["valueType"] == 'nil' || $symb2["valueType"] == 'nil') {
            throw new OperandTypeException;
        }
        
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkString($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkString($this->resolve->resolveSymbolType($symb2));

        $value = $comp1 . $comp2;
        $this->table->assignValue($varName, $value);
        
    }

    public function GETCHAR(mixed $varName, mixed $symb1, mixed $symb2) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkString($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb2));

        if($symb1['valueType'] == 'nil')
        {
            throw new OperandTypeException;
        }

        if ($comp2 < 0 || $comp2 >= strlen($comp1)) {
            throw new StringOperationException;
        }
    
        // Get the character at the specified index
        $char = substr($comp1, $comp2, 1);
    
        // Store the character in the variable $varName
        $this->table->assignValue($varName['value'], $char);
    }

    public function SETCHAR(mixed $varName, mixed $symb1, mixed $symb2) : void  {

        $var = $this->resolve->resolveSymbol($varName);
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkString($this->resolve->resolveSymbolType($symb2));
        TypeCheck::checkString($this->resolve->resolveSymbolType($varName));

        if ($comp1 < 0 || $comp1 >= strlen($var)) {
            throw new StringOperationException;
        }

        $first = substr($comp2, 0, 1);
    
        $value = substr_replace($var, $first, $comp1, 1);
    
        $this->table->assignValue($varName['value'], $value);
    }

    public function READ(mixed $varName, mixed $type) : void  {

        if ($type['type'] != 'type') {
            throw new SemanticErrorException;
        }

        $comp1 = $this->resolve->resolveSymbol($type);

        switch($comp1)
        {
            case 'int':
                $value = $this->readerIn->readInt();
                break;
            case 'string':
                $value = $this->readerIn->readString();
                break;
            case 'bool':
                $value = $this->readerIn->readBool();
                break;
            default:
                $value = 'nil@nil';
                break;
        }
        $this->table->assignValue($varName['value'], $value);
    }

    public function WRITE(mixed $symb) : void {
        switch ($symb['type']) {
            case 'int':
            case 'string':
            case 'bool':
                $value = $symb['value'];
                break;
            case 'nil':
                $value = '';
                break;
            case 'var':
                $value = $this->table->getVariableValue($symb['value']);
                if($this->resolve->resolveSymbolType($symb) == 'nil') {
                    $value = '';
                }
                break;
            default:
                throw new SemanticErrorException;

        }

        if(is_bool($value)){
            $value = $value ?'true':'false';
        }

        $this->stdout->writeString($this->parseString("$value"));
    }

    public function DPRINT(mixed $symb1) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);

        $this->stderr->writeString($comp1);
    }

    public function PUSHS(mixed $symb1) : void {
        $comp1 = $this->resolve->resolveValue($symb1);
        $this->stack[] = $comp1;
    }

    public function POPS(mixed $symb) : void {
        if (!empty($this->stack)) {
            $this->table->assignValue($symb['value'], array_pop($this->stack));
        } else {
            throw new ValueErrorException;
        }
    }

    public function INT2CHAR(mixed $var, mixed $symb1) : void {

        $comp1 = $this->resolve->resolveSymbol($symb1);

        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb1));

        if ($comp1 < 0 || $comp1 > 255) {
            throw new StringOperationException;
        }

        $unicode = chr($comp1);
        $this->table->assignValue($var['value'], $unicode);
    }
    
    public function STRI2INT(mixed $var, mixed $symb1, mixed $symb2) : void {
        $comp1 = $this->resolve->resolveSymbol($symb1);
        $comp2 = $this->resolve->resolveSymbol($symb2);

        TypeCheck::checkString($this->resolve->resolveSymbolType($symb1));
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb2));

        if ($comp2 < 0 || $comp2 >= strlen($comp1)) { 
            throw new StringOperationException;
        }
    
        $ordinalValue = ord($comp1[$comp2]);
    
        $this->table->assignValue($var['value'], $ordinalValue);
    }
    public function EXIT(mixed $symb1) : int {
        $comp1 = $this->resolve->resolveSymbol($symb1);

        
        TypeCheck::checkInt($this->resolve->resolveSymbolType($symb1));

        if ($comp1 < 0 || $comp1 > 9) {
            throw new OperandValueException;
        }

        return $comp1;
    }

    public function parseString(string $string) : string {

        $parsedString = preg_replace_callback('/\\\\([0-9]{3})/', function ($matches) {
            return chr(intval($matches[1]));
        }, $string);
    
        return $parsedString;
    }
}
