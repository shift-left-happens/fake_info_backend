<?php
namespace App;
class Words
{
    public function letterOccurrences($input)
    {
        $char_counts = [];
        $length = strlen($input);
        for ( $i = 0; $i < $length; $i++){
            $char = $input[$i];

            $char_counts[$char] = isset($char_counts[$char]) ? $char_counts[$char]+1 : 1;
        }
        return $char_counts;
    }
}