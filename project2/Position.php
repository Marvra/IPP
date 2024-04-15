<?php 
namespace IPP\Student;

class Position{
    public int $positionCounter = 0;

    public function setPositionCounter(int $position) : void {
        $this->positionCounter = $position;
    }
    public function getPositionCounter() : int {
        return $this->positionCounter;
    }

    public function incrementPositionCounter() : void {
        $this->positionCounter++;
    }
}
