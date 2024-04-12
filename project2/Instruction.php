<?php
namespace IPP\Student;

use IPP\Core\Settings;
use IPP\Student\TypeCheck;
use IPP\Student\Frame;

use IPP\Student\ExceptionExtended\FrameAccessException;
use IPP\Student\ExceptionExtended\OperandTypeException;
use IPP\Student\ExceptionExtended\InvalidSourceException;
use IPP\Student\ExceptionExtended\OperandValueException;
use IPP\Student\ExceptionExtended\SemanticErrorException;
use IPP\Student\ExceptionExtended\StringOperationException;
use IPP\Student\ExceptionExtended\ValueErrorException;
use IPP\Student\ExceptionExtended\VariableAccessException;

/**
 * Input reader that reads from a file
 */
class Instruction
{
    public $table;
    public $readerIn;
    public $stdout;
    public $stderr;
    public $positionCounter = 0;
    public $stack = [];
    //instructions

    public function __construct(Settings $settings) {
        $this->table = new Frame();
        $this->readerIn = $settings->getInputReader();
        $this->stdout = $settings->getStdOutWriter();
        $this->stderr = $settings->getStdErrWriter();
    }
    public function setPositionCounter($position) {
        $this->positionCounter = $position;
    }
    public function getPositionCounter() : int {
        return $this->positionCounter;
    }

    public function incrementPositionCounter() {
        $this->positionCounter++;
    }

    public function JUMP($labelName, $lableTable) {
        $psCounter = $lableTable->getPositionCounterForLabel($labelName);
        return $psCounter;
    }

    public function CALL($labelName, $lableTable) {
        $psCounter = $lableTable->getPositionCounterForLabel($labelName);
        return $psCounter;
    }

    public function JUMPIFEQ($labelName, $symb1, $symb2, $lableTable) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::equalType($comp1, $comp2);

        if ($comp1 == $comp2) {
            return $lableTable->getPositionCounterForLabel($labelName);
        } else {
            return $this->getPositionCounter();
        }
    }

    public function JUMPIFNEQ($labelName, $symb1, $symb2, $lableTable) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::equalType($comp1, $comp2);
        
        if ($comp1 != $comp2) {
            return $lableTable->getPositionCounterForLabel($labelName);
        } else {
            return $this->getPositionCounter();
        }
    }

    public function DEFVAR($varName){
        $errCode = $this->table->defineVariable($varName);
        return $errCode;

    }

    public function ADD($varName, $symb1, $symb2){
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkInt($comp1);
        TypeCheck::checkInt($comp2);

        $value = $comp1 + $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function MUL($varName, $symb1, $symb2){
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkInt($comp1);
        TypeCheck::checkInt($comp2);

        $value = $comp1 * $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function SUB($varName, $symb1, $symb2){
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkInt($comp1);
        TypeCheck::checkInt($comp2);

        $value = $comp1 - $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function IDIV($varName, $symb1, $symb2){
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);
    
        TypeCheck::checkInt($comp1);
        TypeCheck::checkInt($comp2);

        if ($comp2 == 0) {
            throw new OperandValueException;
        }
    
        $value = $comp1 / $comp2;
        $this->table->assignValue($varName, $value);
    }

    public function STRLEN($varName, $symb1){
        $comp1 = $this->resolveSymbol($symb1);

        TypeCheck::checkString($comp1);

        $errCode = $this->table->assignValue($varName['value'], (int)strlen($comp1));
        return $errCode;
    }

    public function CREATEFRAME(){
       
    }

    public function PUSHFRAME(){
       
    }

    public function POPFRAME(){
       
    }

    public function MOVE($varName, $value){
        $value = $this->resolveSymbol($value);
        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }


    public function AND($varName, $symb1, $symb2) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkBool($comp1);
        TypeCheck::checkBool($comp2);

        $value = $comp1 && $comp2  ? true : false;

        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }
    
    public function OR($varName, $symb1, $symb2) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkBool($comp1);
        TypeCheck::checkBool($comp2);

        $value = $comp1 || $comp2  ? true : false;

        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }
    
    public function NOT($varName, $symb1) {
        $comp1 = $this->resolveSymbol($symb1);

        TypeCheck::checkBool($comp1);

        $value = !$comp1 ? true : false;

        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }

    public function LT($varName, $symb1, $symb2) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::equalType($comp1, $comp2);
    
        $value = ($comp1 < $comp2) ? 'true' : 'false';
        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }
    
    public function GT($varName, $symb1, $symb2) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);
    
        // echo "Type 1 : " . gettype($comp1) . PHP_EOL;
        // echo "Type 2 : " . gettype($comp2) . PHP_EOL;

        TypeCheck::equalType($comp1, $comp2);
    
        $value = ($comp1 > $comp2) ? 'true' : 'false';
        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }
    
    public function EQ($varName, $symb1, $symb2) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);
    
        // Check if the types are the same

        TypeCheck::equalType($comp1, $comp2);
    
        $value = ($comp1 === $comp2) ? true : false;
        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }

    public function TYPE($varName, $symb){
        if ($symb['type'] == 'var')
        {
            $value = $this->table->getVariableValue($symb['value']);
            if ($value == null)
            {
                $value = "";
            }
            // try{
            //     $value = $this->table->getVariableValue($symb['value']);
            // }
            // catch(Exception){
            //     $value = '';
            // }
        }
        else
        {
            $value = $symb['type'];
        }
        $this->table->assignValue($varName['value'], "$value");
        
    }

    public function CONCAT($varName, $symb1, $symb2){
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkString($comp1);
        TypeCheck::checkString($comp2);

        $value = $comp1 . $comp2;
        $errCode = $this->table->assignValue($varName, $value);
        return $errCode;
    }

    public function GETCHAR($varName, $symb1, $symb2){
        // Get the string value from $symb1
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkString($comp1);
        TypeCheck::checkInt($comp2);

        if ($comp2 < 0 || $comp2 >= strlen($comp1)) {
            throw new StringOperationException;
        }
    
        // Get the character at the specified index
        $char = substr($comp1, $comp2, 1);
    
        // Store the character in the variable $varName
        $this->table->assignValue($varName['value'], $char);
    }

    public function SETCHAR($varName, $symb1, $symb2){

        $var = $this->resolveSymbol($varName);
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkInt($comp1);
        TypeCheck::checkString($comp2);
        TypeCheck::checkString($var);
        
        /// !!!! TOOT BY ASI ZORAVNA NEMALO BYT "hah" ALE ASI VAR !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        if ($comp1 < 0 || $comp1 >= strlen("hah")) {
            throw new StringOperationException;
        }

        $first = substr($comp2, 0, 1);
    
        // // Get the character at the specified index
        $value = substr_replace($var, $first, $comp1, 1);
    
        // Store the character in the variable $varName
        $this->table->assignValue($varName['value'], $value);
    }

    public function READ($varName, $type){
        $comp1 = $this->resolveSymbol($type);

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
        // if ($value == null)
        // {
        //     $value = 'nil@nil';
        // }
        $this->table->assignValue($varName['value'], $value);
    }

    public function WRITE($symb) {
        switch ($symb['type']) {
            case 'int':
            case 'string':
                $value = $symb['value'];
                break;
            case 'var':
                $value = $this->table->getVariableValue($symb['value']);
                // if ($value === null)
                // {
                //     throw new Exception('Missing value Mate', ReturnCode::VALUE_ERROR);
                // }
                break;
            default:
                throw new SemanticErrorException;

        }
        // echo "\n$value\n";
        if(is_bool($value)){
            $value = $value ?'true':'false';
        }

        // if($value == null)
        // {
        //     throw new ValueErrorException;
        // }

        $this->stdout->writeString($this->parseString("$value"));
    }

    public function DPRINT($symb1) {
        $comp1 = $this->resolveSymbol($symb1);

        $this->stderr->writeString($comp1);
    }

    public function PUSHS($symb1) {
        $comp1 = $this->resolveSymbol($symb1);
        $this->stack[] = $comp1;
    }

    public function POPS($symb) {
        if (!empty($this->stack)) {
            $this->table->assignValue($symb['value'], array_pop($this->stack));
        } else {
            throw new ValueErrorException;
        }
    }

    public function INT2CHAR($var, $symb1) {
        $comp1 = $this->resolveSymbol($symb1);

        TypeCheck::checkInt($comp1);

        if ($comp1 < 0 || $comp1 > 255) {
            throw new StringOperationException;
        }

        $unicode = chr($comp1);
        $this->table->assignValue($var['value'], $unicode);
    }
    
    public function STRI2INT($var, $symb1, $symb2) {
        $comp1 = $this->resolveSymbol($symb1);
        $comp2 = $this->resolveSymbol($symb2);

        TypeCheck::checkString($comp1);
        TypeCheck::checkInt($comp2);

        // Check if $comp2 is within the bounds of the string
        if ($comp2 < 0 || $comp2 >= strlen($comp1)) { 
            throw new StringOperationException;
        }
    
        // Get the character at the specified position and convert it to its Unicode ordinal value
        $ordinalValue = ord($comp1[$comp2]);
    
        // Assign the ordinal value to the variable
        $this->table->assignValue($var['value'], $ordinalValue);
    }
    public function EXIT($symb1) {
        $comp1 = $this->resolveSymbol($symb1);

        
        TypeCheck::checkInt($comp1);

        if ($comp1 < 0 || $comp1 > 9) {
            throw new OperandValueException;
        }

        return $comp1;
    }

    public function parseString($string){

        $parsedString = preg_replace_callback('/\\\\([0-9]{3})/', function ($matches) {
            return chr(intval($matches[1])); // Convert octal number to character
        }, $string);
    
        return $parsedString;

    }

    protected function resolveSymbol($symb) {
        if ($symb['type'] == 'var') {
            return $this->table->getVariableValue($symb['value']);
        } else {
            return $symb['value'];
        }
    }

}
