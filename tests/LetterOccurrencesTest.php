<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Words;

class LetterOccurrencesTest extends TestCase
{
    public function testLetterOccurrencesReturnsArray() {
        $words = new Words();
        $result = $words->letterOccurrences("");

        $this->assertIsArray($result);
    }

    public function testLetterOccurrencesForBetter() {
        $words = new Words();
        $test_word = "hello";
        $expected_result = [
            'h' => 1,
            'e' => 1,
            'l' => 2,
            'o' => 1
        ];
        $result = $words->letterOccurrences($test_word);
        print_r($expected_result);
        $this->assertEquals($expected_result, $result);
    }
}
# Return a data structure
# Count occurrences of each letter in a string
# Return an associative array with letters as keys and their counts as values
# Accept any word as input and return the correct counts for each letter