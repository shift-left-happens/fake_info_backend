<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/Town.php';

/**
 * Tests for the Town class.
 *
 * Town talks directly to the database, so these are integration tests.
 * They verify that getRandomTown() returns valid data from the postal_code table.
 */
class TownTest extends TestCase
{
    private \Town $town;

    protected function setUp(): void
    {
        $this->town = new \Town();
    }

    // ── Structure ────────────────────────────────

    /** getRandomTown() must return an array. */
    public function testGetRandomTownReturnsArray(): void
    {
        $result = $this->town->getRandomTown();

        $this->assertIsArray($result);
    }

    /** The returned array must contain exactly the two expected keys. */
    public function testGetRandomTownContainsExpectedKeys(): void
    {
        $result = $this->town->getRandomTown();

        $this->assertArrayHasKey('postal_code', $result);
        $this->assertArrayHasKey('town_name', $result);
    }

    // ── Postal code format ───────────────────────

    /** Postal code must be exactly 4 digits. */
    public function testPostalCodeFormat(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $result = (new \Town())->getRandomTown();

            $this->assertMatchesRegularExpression(
                '/^\d{4}$/',
                $result['postal_code'],
                "Invalid postal code: '{$result['postal_code']}'"
            );
        }
    }

    // ── Town name ────────────────────────────────

    /** Town name must be a non-empty string. */
    public function testTownNameIsNonEmpty(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $result = (new \Town())->getRandomTown();

            $this->assertIsString($result['town_name']);
            $this->assertNotEmpty($result['town_name']);
        }
    }

    // ── Randomness ───────────────────────────────

    /** Multiple calls should return different results (not hardcoded). */
    public function testReturnsDifferentResults(): void
    {
        $seen = [];

        for ($i = 0; $i < 50; $i++) {
            $result = (new \Town())->getRandomTown();
            $seen[$result['postal_code']] = true;
        }

        $this->assertGreaterThan(
            1,
            count($seen),
            'getRandomTown() returned the same postal code 50 times in a row'
        );
    }

    // ── DB pair validation ───────────────────────

    /** The returned (postal_code, town_name) pair must exist in the database. */
    public function testReturnedPairExistsInDatabase(): void
    {
        $dbName = getenv('DB_NAME') ?: 'addresses';
        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD');
        if ($password === false) {
            $password = '';
        }

        $pdo = new \PDO(
            "mysql:host=$host;dbname=$dbName;charset=utf8",
            $user,
            $password,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        for ($i = 0; $i < 20; $i++) {
            $result = (new \Town())->getRandomTown();

            $stmt = $pdo->prepare(
                'SELECT COUNT(*) AS cnt FROM postal_code WHERE cPostalCode = :pc AND cTownName = :tn'
            );
            $stmt->execute([
                ':pc' => $result['postal_code'],
                ':tn' => $result['town_name'],
            ]);
            $count = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            $this->assertGreaterThanOrEqual(
                1,
                $count,
                "Pair not in DB: {$result['postal_code']} / {$result['town_name']}"
            );
        }
    }
}
