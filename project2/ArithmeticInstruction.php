

// namespace IPP\Student;

// require_once 'student/Instruction.php';
// use Exception;
// use IPP\Core\ReturnCode;

// class ArithmeticInstruction extends Instruction
// {
//     public function __construct()
//     {
//     }
//     public function ADD($varName, $symb1, $symb2){
//         $comp1 = $this->resolveSymbol($symb1);
//         $comp2 = $this->resolveSymbol($symb2);

//         if (!is_int($comp1) || !is_int($comp2)) {
//             throw new Exception('Bad Type aaaaate', ReturnCode::OPERAND_TYPE_ERROR);
//         }

//         $value = $comp1 + $comp2;
//         $this->table->assignValue($varName, $value);
//     }

//     public function MUL($varName, $symb1, $symb2){
//         $comp1 = $this->resolveSymbol($symb1);
//         $comp2 = $this->resolveSymbol($symb2);

//         if (!is_int($comp1) || !is_int($comp2)) {
//             throw new Exception('Bad Type aaaaate', ReturnCode::OPERAND_TYPE_ERROR);
//         }

//         $value = $comp1 * $comp2;
//         $this->table->assignValue($varName, $value);
//     }

//     public function SUB($varName, $symb1, $symb2){
//         $comp1 = $this->resolveSymbol($symb1);
//         $comp2 = $this->resolveSymbol($symb2);

//         if (!is_int($comp1) || !is_int($comp2)) {
//             throw new Exception('Bad Type aaaaate', ReturnCode::OPERAND_TYPE_ERROR);
//         }

//         $value = $comp1 - $comp2;
//         $this->table->assignValue($varName, $value);
//     }

//     public function IDIV($varName, $symb1, $symb2){
//         $comp1 = $this->resolveSymbol($symb1);
//         $comp2 = $this->resolveSymbol($symb2);
    
//         if (!is_int($comp1) || !is_int($comp2)) {
//             throw new Exception('Bad Type aaaaate', ReturnCode::OPERAND_TYPE_ERROR);
//         }
//         if ($comp2 == 0) {
//             throw new Exception('Division by zero', ReturnCode::OPERAND_VALUE_ERROR);
//         }
    
//         $value = $comp1 / $comp2;
//         $this->table->assignValue($varName, $value);
//     }
// }