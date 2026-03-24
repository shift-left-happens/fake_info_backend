 /** Note:
 * There is some repetition in creating the FakeInfo object and retrieving data.
 * It was suggested to use a PHPUnit Data Provider to reduce repetition when
 * testing multiple similar fields (e.g. firstName, lastName, gender).
 *
 * A data provider allows a single test method to run multiple times with
 * different inputs. For example:
 *
 *   testFieldExists($field)
 *   → runs for 'firstName', 'lastName', 'gender'
*/

<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/FakeInfo.php';

class FakeInfoTest extends TestCase
{
    public function testFirstNameExists()
    {
        $fakeInfo = new FakeInfo();
        $result = $fakeInfo->getFullNameAndGender();

        $this->assertArrayHasKey('firstName', $result);
    }

    public function testFirstNameIsString()
{
    $fakeInfo = new FakeInfo();
    $result = $fakeInfo->getFullNameAndGender();

    $this->assertIsString($result['firstName']);
}

    public function testFirstNameNotEmpty()
{
    $fakeInfo = new FakeInfo();
    $result = $fakeInfo->getFullNameAndGender();

    $this->assertNotEmpty($result['firstName']);
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

public function testFirstNameNoNumbers()
{
    $fakeInfo = new FakeInfo();
    $result = $fakeInfo->getFullNameAndGender();

    $this->assertDoesNotMatchRegularExpression('/[0-9]/', $result['firstName']);
}


}