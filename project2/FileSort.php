<?php
namespace IPP\Student;

use IPP\Student\ExceptionExtended\InvalidSourceException;
use DOMDocument;

class FileSort
{
    public static function SortByOrder(DOMDocument $unsortedFile) : array 
    {
        $instructionsArray = iterator_to_array($unsortedFile->getElementsByTagName("instruction"));

        $callbackForUsort = function($a, $b) {
            $orderA = intval($a->getAttribute("order"));
            $orderB = intval($b->getAttribute("order"));
            $returnValue = $orderA - $orderB;
            if ($returnValue == 0 ||  $orderA <= 0 || $orderB <= 0) {
                throw new InvalidSourceException;
            }
            return $returnValue;
        };

        usort($instructionsArray, $callbackForUsort);
        return $instructionsArray;
    }
}
