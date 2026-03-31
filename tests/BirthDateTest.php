<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\FakeInfo;

require_once __DIR__ . '/../src/FakeInfo.php';

class BirthDateTest extends TestCase
{
    private FakeInfo $fakeInfo;
    private string $birthDate;

    protected function setUp(): void
    {
        $this->fakeInfo = new FakeInfo();
        $this->birthDate = $this->fakeInfo->getFullNameGenderAndBirthDate()['birthDate'];
    }

    // Splits a 'YYYY-MM-DD' string into year, month, and day as integers.
    private function extractDateParts(string $birthDate): array
    {
        $parts = explode('-', $birthDate); // Potential bug: 1995-08-24-123123123 -> ['1995', '08', '24', '123123123']
        return [
            'year' => (int) $parts[0],
            'month' => (int) $parts[1],
            'day' => (int) $parts[2],
        ];
    }

    // Output must be formatted as YYYY-MM-DD.
    public function testBirthDateFormat(): void
    {
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $this->birthDate);
    }

    // Year must be between 1900 and the current year.
    public function testYearRange(): void
    {
        $parts = $this->extractDateParts($this->birthDate);

        $this->assertGreaterThanOrEqual(1900, $parts['year']);
        $this->assertLessThanOrEqual((int) date('Y'), $parts['year']);
    }

    // Month must be between 1 and 12.
    public function testMonthRange(): void
    {
        $parts = $this->extractDateParts($this->birthDate);

        $this->assertGreaterThanOrEqual(1, $parts['month']);
        $this->assertLessThanOrEqual(12, $parts['month']);
    }

    // All 3 branches must be reachable and produce the correct day range.
    // Runs 1000 iterations because February only has a 1/12 chance of being picked.
    public function testAllBranchesCovered(): void
    {
        $hit31Day = false;
        $hit30Day = false;
        $hitFebruary = false;

        $months31 = [1, 3, 5, 7, 8, 10, 12];
        $months30 = [4, 6, 9, 11];

        for ($i = 0; $i < 1000; $i++) {
            $fakeInfo = new FakeInfo();
            $birthDate = $fakeInfo->getFullNameGenderAndBirthDate()['birthDate'];
            $parts = $this->extractDateParts($birthDate);

            $this->assertGreaterThanOrEqual(1, $parts['day']);

            if (in_array($parts['month'], $months31)) {
                $hit31Day = true;
                $this->assertLessThanOrEqual(31, $parts['day']);
            } elseif (in_array($parts['month'], $months30)) {
                $hit30Day = true;
                $this->assertLessThanOrEqual(30, $parts['day']);
            } else {
                $hitFebruary = true;
                $this->assertLessThanOrEqual(28, $parts['day']);
            }
        }

        $this->assertTrue($hit31Day, 'Branch 1 (31-day months) was never hit in 1000 iterations');
        $this->assertTrue($hit30Day, 'Branch 2 (30-day months) was never hit in 1000 iterations');
        $this->assertTrue($hitFebruary, 'Branch 3 (February) was never hit in 1000 iterations');
    }

    // birthDate is valid for every person returned by getFakePersons.
    public function testBirthDateInBulk(): void
    {
        $persons = $this->fakeInfo->getFakePersons(100);
        foreach ($persons as $person) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $person['birthDate']);
        }
    }

    // birthDate is present and valid in the CPR+name+gender+dob response.
    public function testBirthDateInCprResponse(): void
    {
        $birthDate = $this->fakeInfo->getCprFullNameGenderAndBirthDate()['birthDate'];
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $birthDate);
    }

    // birthDate is present and valid in the single-person response.
    public function testBirthDateInSinglePerson(): void
    {
        $birthDate = $this->fakeInfo->getFakePerson()['birthDate'];
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $birthDate);
    }
}
