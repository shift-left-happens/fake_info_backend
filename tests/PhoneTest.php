<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\FakeInfo;

class PhoneTest extends TestCase
{
    private FakeInfo $fakeInfo;
    private array $allowedPrefixes = [
        2, 30, 31, 40, 41, 42, 50, 51, 52, 53,
        60, 61, 71, 81, 91, 92, 93, 342, 344, 345, 346, 347,
        348, 349, 356, 357, 359, 362, 365, 366, 389, 398,
        431, 441, 462, 466, 468, 472, 474, 476, 478, 485, 486,
        488, 489, 493, 494, 495, 496, 498, 499, 542, 543, 545,
        551, 552, 556, 571, 572, 573, 574, 577, 579, 584, 586,
        587, 589, 597, 598, 627, 629, 641, 649, 658, 662, 663,
        664, 665, 667, 692, 693, 694, 697, 771, 772, 782, 783,
        785, 786, 788, 789, 826, 827, 829
    ];

    protected function setUp(): void
    {
        $this->fakeInfo = new FakeInfo();
    }

    // ------------------ getPhoneNumber() tests ------------------

    public function testPhoneLength(): void
    {
        $phone = $this->fakeInfo->getPhoneNumber();
        $this->assertEquals(8, strlen($phone), "Phone number length should be 8");
    }

    public function testPhoneIsDigits(): void
    {
        $phone = $this->fakeInfo->getPhoneNumber();
        $this->assertTrue(ctype_digit($phone), "Phone number should contain only digits");
    }

    public function testPhonePrefix(): void
    {
        $phone = $this->fakeInfo->getPhoneNumber();
        $allowed = false;
        foreach ($this->allowedPrefixes as $prefix) {
            if (str_starts_with($phone, $prefix)) {
                $allowed = true;
                break;
            }
        }
        $this->assertTrue($allowed, "Phone prefix is not allowed");
    }

    // ------------------ getFakePerson()['phoneNumber'] tests ------------------

    public function testPersonPhoneLength(): void
    {
        $phone = $this->fakeInfo->getFakePerson()['phoneNumber'];
        $this->assertEquals(8, strlen($phone), "Person phone number length should be 8");
    }

    public function testPersonPhoneIsDigits(): void
    {
        $phone = $this->fakeInfo->getFakePerson()['phoneNumber'];
        $this->assertTrue(ctype_digit($phone), "Person phone number should contain only digits");
    }

    public function testPersonPhonePrefix(): void
    {
        $phone = $this->fakeInfo->getFakePerson()['phoneNumber'];
        $allowed = false;
        foreach ($this->allowedPrefixes as $prefix) {
            if (str_starts_with($phone, (string) $prefix)) {
                $allowed = true;
                break;
            }
        }
        $this->assertTrue($allowed, "Person phone prefix is not allowed");
    }
}