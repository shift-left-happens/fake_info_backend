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

 <?php

    use PHPUnit\Framework\Attributes\DataProvider;
    use PHPUnit\Framework\TestCase;

    require_once __DIR__ . '/../src/FakeInfo.php';

    class FakeInfoTest extends TestCase
    {

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
        public function testExists($fieldname, $method)
        {
            $fakeInfo = new FakeInfo();
            $result = $fakeInfo->$method();

            $this->assertArrayHasKey($fieldname, $result);
        }

        #[DataProvider('getTestData')]
        public function testIsString($fieldname, $method)
        {
            $fakeInfo = new FakeInfo();
            $result = $fakeInfo->$method();

            $this->assertIsString($result[$fieldname]);
        }

        #[DataProvider('getTestData')]
        public function testIsNotEmpty($fieldname, $method)
        {
            $fakeInfo = new FakeInfo();
            $result = $fakeInfo->$method();

            $this->assertNotEmpty($result[$fieldname]);
        }

        public function testFirstNameFormat()
        {
            $fakeInfo = new FakeInfo();
            $result = $fakeInfo->getFullNameAndGender();

            $pattern = '/^[A-Za-zÆØÅæøå]+ [A-Z]\.$/';

            $this->assertMatchesRegularExpression(
                $pattern,
                $result['firstName']
            );
        }

        public function testLastNameFormat()
        {
            $fakeInfo = new FakeInfo();
            $result = $fakeInfo->getFullNameAndGender();

            $pattern = '/^[A-Za-zÆØÅæøå]+$/';

            $this->assertMatchesRegularExpression(
                $pattern,
                $result['lastName']
            );
        }

        public function testFirstNameNoNumbers()
        {
            $fakeInfo = new FakeInfo();
            $result = $fakeInfo->getFullNameAndGender();

            $this->assertDoesNotMatchRegularExpression('/[0-9]/', $result['firstName']);
        }
    }
