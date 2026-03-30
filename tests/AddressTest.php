<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\FakeInfo;

require_once __DIR__ . '/../src/FakeInfo.php';

/**
 * Address tests for FakeInfo::getAddress().
 *
 * Black-box: equivalence partitioning, boundary value analysis, decision table
 * White-box: branch coverage (all 6 door branches, both floor branches)
 */
class AddressTest extends TestCase
{
    private FakeInfo $fakeInfo;

    protected function setUp(): void
    {
        $this->fakeInfo = new FakeInfo();
    }

    // ── Structure ────────────────────────────────

    public function testGetAddressReturnsArrayWithAddressKey(): void
    {
        $result = $this->fakeInfo->getAddress();

        $this->assertArrayHasKey('address', $result);
        $this->assertIsArray($result['address']);
    }

    
    public static function addressProvider(): array
    {
        return [
            ['getAddress', 'address'],
            ['getFakePerson', 'address'],
        ];
    }
    #[DataProvider('addressProvider')]
    public function testAddressContainsAllExpectedKeys($method, $field): void
    {
        $address = $this->fakeInfo->$method()[$field];
        $expected = ['street', 'number', 'floor', 'door', 'postal_code', 'town_name'];

        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $address, "Missing key: $key");
        }
        $this->assertCount(count($expected), $address);
    }

    #[DataProvider('addressProvider')]
    public function testFakePersonAddressHasSameStructure($method, $field): void
    {
        $address = $this->fakeInfo->$method()[$field];
        $expected = ['street', 'number', 'floor', 'door', 'postal_code', 'town_name'];

        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $address, "Missing key in person address: $key");
        }
    }

    // ── Street ───────────────────────────────────

    /** Street: non-empty, alphabetic + spaces + Danish chars, no leading space. */
    #[DataProvider('addressProvider')]
    public function testStreetFormat($method, $field): void
    {
        for ($i = 0; $i < 50; $i++) {
            $street = (new FakeInfo())->$method()[$field]['street'];

            $this->assertMatchesRegularExpression(
                '/^[A-Za-zÆØÅæøå][A-Za-zÆØÅæøå ]*$/u',
                $street,
                "Invalid street: '$street'"
            );
        }
    }

    // ── Number ───────────────────────────────────

    /** Number: 1–999 optionally followed by one uppercase letter. */
    #[DataProvider('addressProvider')]
    public function testNumberFormat($method, $field): void
    {
        for ($i = 0; $i < 100; $i++) {
            $number = (string) (new FakeInfo())->$method()[$field]['number'];

            $this->assertMatchesRegularExpression(
                '/^[1-9]\d{0,2}[A-Z]?$/',
                $number,
                "Invalid number: '$number'"
            );
        }
    }

    /** ~20% of addresses should have a letter suffix. */
    #[DataProvider('addressProvider')]
    public function testNumberLetterSuffixAppears($method, $field): void
    {
        $found = false;
        for ($i = 0; $i < 200 && !$found; $i++) {
            $number = (string) (new FakeInfo())->$method()[$field]['number'];
            if (preg_match('/[A-Z]$/', $number)) {
                $found = true;
            }
        }
        $this->assertTrue($found, 'No letter suffix found in 200 iterations');
    }

    // ── Floor ────────────────────────────────────

    /** Floor: "st" or integer 1–99. */
    #[DataProvider('addressProvider')]
    public function testFloorFormat($method, $field): void
    {
        for ($i = 0; $i < 100; $i++) {
            $floor = (string) (new FakeInfo())->$method()[$field]['floor'];

            $this->assertMatchesRegularExpression(
                '/^(st|[1-9]\d?)$/',
                $floor,
                "Invalid floor: '$floor'"
            );
        }
    }

    /** Both floor branches ("st" and numeric) must be reachable. */
    #[DataProvider('addressProvider')]
    public function testFloorBothBranchesCovered($method, $field): void
    {
        $hitSt = false;
        $hitNumeric = false;

        for ($i = 0; $i < 200; $i++) {
            $floor = (string) (new FakeInfo())->$method()[$field]['floor'];

            $hitSt = $hitSt || ($floor === 'st');
            $hitNumeric = $hitNumeric || ($floor !== 'st');

            if ($hitSt && $hitNumeric) {
                break;
            }
        }

        $this->assertTrue($hitSt, '"st" floor never generated');
        $this->assertTrue($hitNumeric, 'Numeric floor never generated');
    }

    // ── Door ─────────────────────────────────────

    /** Door: th|mf|tv | 1-50 | letter(+dash)+1-3 digits. */
    #[DataProvider('addressProvider')]
    public function testDoorFormat($method, $field): void
    {
        for ($i = 0; $i < 200; $i++) {
            $door = (string) (new FakeInfo())->$method()[$field]['door'];

            $this->assertMatchesRegularExpression(
                '/^(th|mf|tv|[1-9]|[1-4]\d|50|[a-zæøå]-?\d{1,3})$/u',
                $door,
                "Invalid door: '$door'"
            );
        }
    }

    /**
     * White-box: all 6 door branches from setAddress() must be hit.
     *   1-7 → "th", 8-14 → "tv", 15-16 → "mf",
     *   17-18 → numeric, 19 → letter+digits, 20 → letter-dash-digits
     */
    #[DataProvider('addressProvider')]
    public function testDoorAllBranchesCovered($method, $field): void
    {
        $hit = ['th' => false, 'tv' => false, 'mf' => false,
                'numeric' => false, 'letterDigits' => false, 'letterDashDigits' => false];

        for ($i = 0; $i < 500; $i++) {
            $door = (string) (new FakeInfo())->$method()[$field]['door'];

            match (true) {
                $door === 'th' => $hit['th'] = true,
                $door === 'tv' => $hit['tv'] = true,
                $door === 'mf' => $hit['mf'] = true,
                (bool) preg_match('/^[a-zæøå]-\d{1,3}$/u', $door) => $hit['letterDashDigits'] = true,
                (bool) preg_match('/^[a-zæøå]\d{1,3}$/u', $door) => $hit['letterDigits'] = true,
                (bool) preg_match('/^\d+$/', $door) => $hit['numeric'] = true,
                default => null,
            };

            if (!in_array(false, $hit, true)) {
                break;
            }
        }

        foreach ($hit as $branch => $wasHit) {
            $this->assertTrue($wasHit, "Door branch '$branch' never generated");
        }
    }

    // ── Postal code & town ───────────────────────

    /** Postal code: exactly 4 digits. */
    #[DataProvider('addressProvider')]
    public function testPostalCodeFormat($method, $field): void
    {
        for ($i = 0; $i < 20; $i++) {
            $postalCode = (new FakeInfo())->$method()[$field]['postal_code'];

            $this->assertMatchesRegularExpression(
                '/^\d{4}$/',
                $postalCode,
                "Invalid postal code: '$postalCode'"
            );
        }
    }

    /** Town name must be a non-empty string. */
    #[DataProvider('addressProvider')]
    public function testTownNameIsNonEmpty($method, $field): void
    {
        for ($i = 0; $i < 20; $i++) {
            $townName = (new FakeInfo())->$method()[$field]['town_name'];

            $this->assertIsString($townName);
            $this->assertNotEmpty($townName);
        }
    }

    // ── Comprehensive (decision table: all-valid) ─

    /** Every generated address must satisfy ALL constraints simultaneously. */
    #[DataProvider('addressProvider')]
    public function testAllFieldsValidSimultaneously($method, $field): void
    {
        for ($i = 0; $i < 100; $i++) {
            $a = (new FakeInfo())->$method()[$field];

            $this->assertMatchesRegularExpression('/^[A-Za-zÆØÅæøå][A-Za-zÆØÅæøå ]*$/u', $a['street']);
            $this->assertMatchesRegularExpression('/^[1-9]\d{0,2}[A-Z]?$/', (string) $a['number']);
            $this->assertMatchesRegularExpression('/^(st|[1-9]\d?)$/', (string) $a['floor']);
            $this->assertMatchesRegularExpression('/^(th|mf|tv|[1-9]|[1-4]\d|50|[a-zæøå]-?\d{1,3})$/u', (string) $a['door']);
            $this->assertMatchesRegularExpression('/^\d{4}$/', $a['postal_code']);
            $this->assertNotEmpty($a['town_name']);
        }
    }
}
