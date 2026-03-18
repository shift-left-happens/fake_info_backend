<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\FakeInfo;

class PhoneTest extends TestCase
{
    public function testPhoneLength(): void
    {
        $fakeInfo = new FakeInfo();
        $phoneNumber = $fakeInfo->getPhoneNumber();
        $len = strlen($phoneNumber);

        $this->assertEquals(8, $len, "Phone numbers length should be 8");
    }
    public function testPhoneNumberIsDigits(): void
    {
        $fakeInfo = new FakeInfo();
        $phoneNumber = $fakeInfo->getPhoneNumber();
        $this->assertTrue(ctype_digit($phoneNumber), "Phone number should contain only digits");
    }
}
