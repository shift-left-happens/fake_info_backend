<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\FakeInfo;

class FakeInfoTest extends TestCase
{
    public function testCprHasTenDigits(): void
    {
        $fakeInfo = new FakeInfo();
        $cpr = $fakeInfo->getCpr();

        $this->assertMatchesRegularExpression('/^\d{9}$/', $cpr);
    }
}
