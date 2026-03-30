<?php

// declare(strict_types=1);

// use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\TestCase;
use App\FakeInfo;

final class CPRTest extends TestCase
{
    private FakeInfo $fakeInfo;
    protected function setUp(): void
    {
        $this->fakeInfo = new FakeInfo();
    }
    // Integrationstest via model: CPR-præfiks skal matche den genererede fødselsdato.
    
    public function testGeneratedCprStartsWithBirthDatePrefix(): void
    {
        $person = $this->fakeInfo->getFullNameGenderAndBirthDate();

        $this->assertStringStartsWith($this->birthDateToCprPrefix($person['birthDate']), $this->fakeInfo->getCpr());
        
        $person = $this->fakeInfo->getFakePerson();

        $this->assertStringStartsWith($this->birthDateToCprPrefix($person['birthDate']), $this->fakeInfo->getCpr());
        
        $person = $this->fakeInfo->getCprFullNameGenderAndBirthDate();

        $this->assertStringStartsWith($this->birthDateToCprPrefix($person['birthDate']), $this->fakeInfo->getCpr());
    }

    // Integrationstest via model: fødselsdatoen har korrekt format.
    public function testGeneratedBirthDateHasExpectedFormat(): void
    {
        $person = $this->fakeInfo->getFullNameGenderAndBirthDate();

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $person['birthDate']);
    }

    // Integrationstest via model: navn og køn bliver genereret med gyldige værdier.
    public function testGeneratedNameAndGenderAreValid(): void
    {
        $fullNameAndGender = $this->fakeInfo->getFullNameAndGender();

        $this->assertIsString($fullNameAndGender['firstName']);
        $this->assertNotSame('', $fullNameAndGender['firstName']);
        $this->assertIsString($fullNameAndGender['lastName']);
        $this->assertNotSame('', $fullNameAndGender['lastName']);
        $this->assertContains($fullNameAndGender['gender'], [FakeInfo::GENDER_FEMININE, FakeInfo::GENDER_MASCULINE]);
    }

    // Integrationstest via model: getFakePerson skal være konsistent med de enkelte getters.
    public function testGetFakePersonHasConsistentCoreValues(): void
    {
        
        $person = $this->fakeInfo->getFakePerson();

        $this->assertSame($this->fakeInfo->getCpr(), $person['CPR']);
        $this->assertContains($person['gender'], [FakeInfo::GENDER_FEMININE, FakeInfo::GENDER_MASCULINE]);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $person['birthDate']);
    }

    // Integrationstest via model: CPR skal være præcis 10 cifre.
    public function testGeneratedCprHasExactLengthTenDigits(): void
    {

        $this->assertMatchesRegularExpression('/^\d{10}$/', $this->fakeInfo->getCpr());
    }

    // Integrationstest via model: genereret køn skal matche parity for CPR's sidste ciffer.
    public function testGeneratedGenderMatchesCprFinalDigitParity(): void
    {

        $fullNameAndGender = $this->fakeInfo->getFullNameAndGender();
        $lastDigit = (int) substr($this->fakeInfo->getCpr(), -1);

        $this->assertContains($fullNameAndGender['gender'], [FakeInfo::GENDER_FEMININE, FakeInfo::GENDER_MASCULINE]);

        if ($fullNameAndGender['gender'] === FakeInfo::GENDER_FEMININE) {
            $this->assertContains($lastDigit, [0, 2, 4, 6, 8], 'For female forventes sidste CPR-ciffer at være lige.');
            return;
        }

        $this->assertContains($lastDigit, [1, 3, 5, 7, 9], 'For male forventes sidste CPR-ciffer at være ulige.');
    }

    // Integrationstest via model: female-profiler skal altid få lige CPR-slutciffer.
    public function testFemaleProfilesAlwaysHaveEvenCprFinalDigit(): void
    {
        $requiredFemaleSamples = 20;
        $femaleDigits = [];
        $mismatchMessage = null;

        for ($attempt = 0; $attempt < 2000 && count($femaleDigits) < $requiredFemaleSamples; $attempt++) {
            $fakeInfo = new FakeInfo();
            $fullNameAndGender = $fakeInfo->getFullNameAndGender();

            if ($fullNameAndGender['gender'] === FakeInfo::GENDER_FEMININE) {
                $digit = (int) substr($fakeInfo->getCpr(), -1);
                if (!in_array($digit, [0, 2, 4, 6, 8], true)) {
                    $mismatchMessage = sprintf(
                        'Female CPR-slutciffer var ulige (%d) ved forsog %d for CPR %s.',
                        $digit,
                        $attempt,
                        $fakeInfo->getCpr()
                    );
                    break;
                }

                $femaleDigits[] = $digit;
            }
        }

        $this->assertNull($mismatchMessage, $mismatchMessage ?? 'Unexpected mismatch i female parity-test.');

        $this->assertCount(
            $requiredFemaleSamples,
            $femaleDigits,
            'Kunne ikke indsamle nok female-profiler til robust parity-test.'
        );

        foreach ($femaleDigits as $digit) {
            $this->assertContains($digit, [0, 2, 4, 6, 8], 'Female CPR-slutciffer skal være lige.');
        }
    }

    // Integrationstest via model: male-profiler skal altid få ulige CPR-slutciffer.
    public function testMaleProfilesAlwaysHaveOddCprFinalDigit(): void
    {
        $requiredMaleSamples = 20;
        $maleDigits = [];
        $mismatchMessage = null;

        for ($attempt = 0; $attempt < 2000 && count($maleDigits) < $requiredMaleSamples; $attempt++) {
            $fakeInfo = new FakeInfo();
            $fullNameAndGender = $fakeInfo->getFullNameAndGender();

            if ($fullNameAndGender['gender'] === FakeInfo::GENDER_MASCULINE) {
                $digit = (int) substr($fakeInfo->getCpr(), -1);
                if (!in_array($digit, [1, 3, 5, 7, 9], true)) {
                    $mismatchMessage = sprintf(
                        'Male CPR-slutciffer var lige (%d) ved forsog %d for CPR %s.',
                        $digit,
                        $attempt,
                        $fakeInfo->getCpr()
                    );
                    break;
                }

                $maleDigits[] = $digit;
            }
        }

        $this->assertNull($mismatchMessage, $mismatchMessage ?? 'Unexpected mismatch i male parity-test.');

        $this->assertCount(
            $requiredMaleSamples,
            $maleDigits,
            'Kunne ikke indsamle nok male-profiler til robust parity-test.'
        );

        foreach ($maleDigits as $digit) {
            $this->assertContains($digit, [1, 3, 5, 7, 9], 'Male CPR-slutciffer skal være ulige.');
        }
    }

    // Integrationstest via model: CPR-præfiks matcher fødselsdatoen konsekvent over flere profiler.
    public function testCprPrefixMatchesBirthDateAcrossMultipleProfiles(): void
    {
        for ($index = 0; $index < 50; $index++) {
            $fakeInfo = new FakeInfo();
            $person = $fakeInfo->getFullNameGenderAndBirthDate();

            $this->assertStringStartsWith(
                $this->birthDateToCprPrefix($person['birthDate']),
                $fakeInfo->getCpr(),
                'CPR-præfiks matcher ikke fødselsdatoen.'
            );
        }
    }

    // Integrationstest via model: bulk-funktionen returnerer korrekt antal og gyldigt CPR-format.
    public function testGetFakePersonsReturnsRequestedAmountWithValidCpr(): void
    {
        $fakeInfo = new FakeInfo();
        $requestedAmount = 10;

        $persons = $fakeInfo->getFakePersons($requestedAmount);

        $this->assertCount($requestedAmount, $persons);

        foreach ($persons as $person) {
            $this->assertArrayHasKey('CPR', $person);
            $this->assertMatchesRegularExpression('/^\d{10}$/', $person['CPR']);
        }
    }

    // Integrationstest via model: CPR-præfiks matcher fødselsdato i bulk-resultater.
    public function testBulkPersonsHaveMatchingBirthDateAndCprPrefix(): void
    {
        $fakeInfo = new FakeInfo();
        $persons = $fakeInfo->getFakePersons(20);

        foreach ($persons as $person) {
            $this->assertArrayHasKey('birthDate', $person);
            $this->assertArrayHasKey('CPR', $person);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $person['birthDate']);
            $this->assertStringStartsWith($this->birthDateToCprPrefix($person['birthDate']), $person['CPR']);
        }
    }

    // Konverterer fødselsdato fra YYYY-MM-DD til CPR-præfiks DDMMYY.
    private function birthDateToCprPrefix(string $birthDate): string
    {
        return substr($birthDate, 8, 2)
            . substr($birthDate, 5, 2)
            . substr($birthDate, 2, 2);
    }
}
