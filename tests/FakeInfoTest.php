<?php

namespace Tests;

/** Note:
 * There is going to be some repetition in creating the FakeInfo object and retrieving data.
 * It was suggested to use a PHPUnit Data Provider to reduce repetition when
 * testing multiple similar fields (e.g. firstName, lastName, gender).
 *
 * A data provider allows a single test method to run multiple times with
 * different inputs. For example:
 *
 * testFieldExists($field)
 * → runs for 'firstName', 'lastName', 'gender'
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use App\FakeInfo;

require_once __DIR__ . '/../src/FakeInfo.php';


class FakeInfoTest extends TestCase
{
    private FakeInfo $fakeInfo;
    protected function setUp(): void
    {
        // This method is called before each test. You can use it to set up common objects or state.
        $this->fakeInfo = new FakeInfo();
    }

    public function testCprHasTenDigits(): void
    {
        $fakeInfo = new FakeInfo();
        $cpr = $fakeInfo->getCpr();

        $this->assertMatchesRegularExpression('/^\d{10}$/', $cpr);
    }

    public static function getTestData(): array
    {
        return [
            // getFullNameAndGender fields
            ['firstName', 'getFullNameAndGender'],
            ['lastName', 'getFullNameAndGender'],
            ['gender', 'getFullNameAndGender'],

            // getFullNameGenderAndBirthDate fields
            ['firstName', 'getFullNameGenderAndBirthDate'],
            ['lastName', 'getFullNameGenderAndBirthDate'],
            ['gender', 'getFullNameGenderAndBirthDate'],
            ['birthDate', 'getFullNameGenderAndBirthDate'],

            // getCprFullNameAndGender fields
            ['CPR', 'getCprFullNameAndGender'],
            ['firstName', 'getCprFullNameAndGender'],
            ['lastName', 'getCprFullNameAndGender'],
            ['gender', 'getCprFullNameAndGender'],

            // getCprFullNameGenderAndBirthDate fields
            ['CPR', 'getCprFullNameGenderAndBirthDate'],
            ['firstName', 'getCprFullNameGenderAndBirthDate'],
            ['lastName', 'getCprFullNameGenderAndBirthDate'],
            ['gender', 'getCprFullNameGenderAndBirthDate'],
            ['birthDate', 'getCprFullNameGenderAndBirthDate'],

            // getFakePerson fields
            ['CPR', 'getFakePerson'],
            ['firstName', 'getFakePerson'],
            ['lastName', 'getFakePerson'],
            ['gender', 'getFakePerson'],
            ['birthDate', 'getFakePerson'],
            ['phoneNumber', 'getFakePerson'],
        ];
    }
    #[DataProvider('getTestData')]
    public function testField($fieldname, $method)
    {
        $result = $this->fakeInfo->$method();

        $this->assertArrayHasKey($fieldname, $result);
        $this->assertIsString($result[$fieldname]);
        $this->assertNotEmpty($result[$fieldname]);
    }

    // #[DataProvider('getTestData')]
    // public function testIsString($fieldname, $method)
    // {
    //     $result = $this->fakeInfo->$method();

    //     $this->assertIsString($result[$fieldname]);
    // }

    // #[DataProvider('getTestData')]
    // public function testIsNotEmpty($fieldname, $method)
    // {
    //     $result = $this->fakeInfo->$method();

    //     $this->assertNotEmpty($result[$fieldname]);
    // }

    public function testFirstNameFormat()
    {
        $result = $this->fakeInfo->getFullNameAndGender();

        $pattern = '/^[A-Za-zÆØÅæøå]+ [A-Z]\.$/';

        $this->assertMatchesRegularExpression(
            $pattern,
            $result['firstName']
        );
    }

    public function testLastNameFormat()
    {
        $result = $this->fakeInfo->getFullNameAndGender();

        $pattern = '/^[A-Za-zÆØÅæøå]+$/';

        $this->assertMatchesRegularExpression(
            $pattern,
            $result['lastName']
        );
    }

    public static function genderProvider(): array
    {
        return [
            // getFullNameAndGender fields
            ['gender', 'getFullNameAndGender'],
            ['gender', 'getFullNameGenderAndBirthDate'],
            ['gender', 'getCprFullNameAndGender'],
            ['gender', 'getCprFullNameGenderAndBirthDate'],
            ['gender', 'getFakePerson'],
        ];
    }
    #[DataProvider('genderProvider')]
    public function testIsValidGender($fieldname, $method)
    {
        $result = $this->fakeInfo->$method();
        $this->assertContains($result[$fieldname], ['male', 'female']);
    }


    public static function noNumbersProvider(): array
    {
        return [
            // getFullNameAndGender fields
            ['firstName', 'getFullNameAndGender'],
            ['lastName', 'getFullNameAndGender'],
            ['gender', 'getFullNameAndGender'],
            ['firstName', 'getFullNameGenderAndBirthDate'],
            ['lastName', 'getFullNameGenderAndBirthDate'],
            ['gender', 'getFullNameGenderAndBirthDate'],
            ['firstName', 'getCprFullNameAndGender'],
            ['lastName', 'getCprFullNameAndGender'],
            ['gender', 'getCprFullNameAndGender'],
            ['firstName', 'getCprFullNameGenderAndBirthDate'],
            ['lastName', 'getCprFullNameGenderAndBirthDate'],
            ['gender', 'getCprFullNameGenderAndBirthDate'],
            ['firstName', 'getFakePerson'],
            ['lastName', 'getFakePerson'],
            ['gender', 'getFakePerson'],
        ];
    }
    #[DataProvider('noNumbersProvider')]
    public function testFirstNameNoNumbers($fieldname, $method)
    {
        $result = $this->fakeInfo->$method();

        $this->assertDoesNotMatchRegularExpression('/[0-9]/', $result[$fieldname]);
    }
}
