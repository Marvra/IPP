<?php
namespace IPP\Student;

use DOMDocument;
use IPP\Core\Exception\InputFileException;
use IPP\Core\Exception\NotImplementedException;
use IPP\Core\Interface\InputReader;
use IPP\Core\ReturnCode;
use Exception;

/**
 * Input reader that reads from a file
 */
class FileSort
{
    public function SortByOrder($unsortedFile) : array 
    {
        $instructionsArray = iterator_to_array($unsortedFile->getElementsByTagName("instruction"));

        $callbackForUsort = function($a, $b) {
            $orderA = intval($a->getAttribute("order"));
            $orderB = intval($b->getAttribute("order"));
            $returnValue = $orderA - $orderB;
            if ($returnValue == 0 ||  $orderA <= 0 || $orderB <= 0) {
                throw new Exception("Invalid order value", ReturnCode::INVALID_SOURCE_STRUCTURE);
            }
            return $returnValue;
        };

        usort($instructionsArray, $callbackForUsort);
        return $instructionsArray;
    }
}
