<?php 
namespace IPP\Student;

use IPP\Student\Frame;

class Resolve{
    public Frame $table;
    public function __construct(Frame $table){
        $this->table = $table;
    }
    
    public function resolveSymbol(mixed $symb) : mixed {
        if ($symb['type'] == 'var') {
            return $this->table->getVariableValue($symb['value']);
        } else {
            return $symb['value'];
        }
    }

    public function resolveSymbolType(mixed $symb) : mixed {
        if ($symb['type'] == 'var') {
            return $this->table->getVariableValueType($symb['value']);
        } else {
            return $symb['valueType'];
        }
    }

    public function resolveValue(mixed $symb) : mixed {
        if ($symb['type'] == 'var') {
            return $this->table->getVariable($symb['value']);
        } else {
            return $symb;
        }
    }
}
