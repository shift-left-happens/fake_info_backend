<?php

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\FakeInfo;

require_once __DIR__ . '/../src/FakeInfo.php';

class FakeInfoTest_backup extends TestCase
{
    private FakeInfo $fakeInfo;

    protected function setUp(): void
    {
        $this->fakeInfo = new FakeInfo();
    }

    /* -------------------------------------------------
     * Helpers
     * -------------------------------------------------*/

    private function assertValidStringField(array $data, string $field): void
    {
        $this->assertArrayHasKey($field, $data);
        $this->assertIsString($data[$field]);
        $this->assertNotEmpty($data[$field]);
    }

    /* -------------------------------------------------
     * CPR Tests
     * -------------------------------------------------*/

    public function testCprIsTenDigits(): void
    {
        $cpr = $this->fakeInfo->getCpr();

        $this->assertMatchesRegularExpression('/^\d{10}$/', $cpr);
    }

    /* -------------------------------------------------
     * Structure Tests (per method)
     * -------------------------------------------------*/

    public function testGetFullNameAndGenderStructure(): void
    {
        $result = $this->fakeInfo->getFullNameAndGender();

        foreach (['firstName', 'lastName', 'gender'] as $field) {
            $this->assertValidStringField($result, $field);
        }
    }

    public function testGetFullNameGenderAndBirthDateStructure(): void
    {
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();

        foreach (['firstName', 'lastName', 'gender', 'birthDate'] as $field) {
            $this->assertValidStringField($result, $field);
        }
    }

    public function testGetCprFullNameAndGenderStructure(): void
    {
        $result = $this->fakeInfo->getCprFullNameAndGender();

        foreach (['CPR', 'firstName', 'lastName', 'gender'] as $field) {
            $this->assertValidStringField($result, $field);
        }
    }

    public function testGetCprFullNameGenderAndBirthDateStructure(): void
    {
        $result = $this->fakeInfo->getCprFullNameGenderAndBirthDate();

        foreach (['CPR', 'firstName', 'lastName', 'gender', 'birthDate'] as $field) {
            $this->assertValidStringField($result, $field);
        }
    }

    public function testGetFakePersonStructure(): void
    {
        $result = $this->fakeInfo->getFakePerson();

        foreach (['CPR', 'firstName', 'lastName', 'gender', 'birthDate', 'phoneNumber'] as $field) {
            $this->assertValidStringField($result, $field);
        }
    }

    /* -------------------------------------------------
     * Format Tests
     * -------------------------------------------------*/

    public function testFirstNameFormat(): void
    {
        $result = $this->fakeInfo->getFullNameAndGender();

        $this->assertMatchesRegularExpression(
            '/^[A-Za-zÆØÅæøå]+ [A-Z]\.$/',
            $result['firstName']
        );
    }

    public function testLastNameFormat(): void
    {
        $result = $this->fakeInfo->getFullNameAndGender();

        $this->assertMatchesRegularExpression(
            '/^[A-Za-zÆØÅæøå]+$/',
            $result['lastName']
        );
    }

    /* -------------------------------------------------
     * Gender Tests
     * -------------------------------------------------*/

    public static function genderProvider(): array
    {
        return [
            ['getFullNameAndGender'],
            ['getFullNameGenderAndBirthDate'],
            ['getCprFullNameAndGender'],
            ['getCprFullNameGenderAndBirthDate'],
            ['getFakePerson'],
        ];
    }

    #[DataProvider('genderProvider')]
    public function testGenderIsValid(string $method): void
    {
        $result = $this->fakeInfo->$method();

        $this->assertContains($result['gender'], ['male', 'female']);
    }

    /* -------------------------------------------------
     * Numeric Field Tests
     * -------------------------------------------------*/

    public static function numericFieldsProvider(): array
    {
        return [
            ['getCprFullNameAndGender', 'CPR', 10],
            ['getCprFullNameGenderAndBirthDate', 'CPR', 10],
            ['getFakePerson', 'CPR', 10],
            ['getFakePerson', 'phoneNumber', 8],
        ];
    }

    #[DataProvider('numericFieldsProvider')]
    public function testNumericFieldsAreDigitsAndCorrectLength(string $method, string $field, int $length): void
    {
        $value = $this->fakeInfo->$method()[$field];

        $this->assertMatchesRegularExpression('/^\d+$/', $value);
        $this->assertSame($length, strlen($value));
    }

    /* -------------------------------------------------
     * No Numbers in Text Fields
     * -------------------------------------------------*/

    public static function textFieldsProvider(): array
    {
        return [
            ['getFullNameAndGender', 'firstName'],
            ['getFullNameAndGender', 'lastName'],
            ['getFullNameGenderAndBirthDate', 'firstName'],
            ['getFullNameGenderAndBirthDate', 'lastName'],
            ['getCprFullNameAndGender', 'firstName'],
            ['getCprFullNameAndGender', 'lastName'],
            ['getCprFullNameGenderAndBirthDate', 'firstName'],
            ['getCprFullNameGenderAndBirthDate', 'lastName'],
            ['getFakePerson', 'firstName'],
            ['getFakePerson', 'lastName'],
        ];
    }

    #[DataProvider('textFieldsProvider')]
    public function testTextFieldsContainNoNumbers(string $method, string $field): void
    {
        $value = $this->fakeInfo->$method()[$field];

        $this->assertDoesNotMatchRegularExpression('/\d/', $value);
    }
}