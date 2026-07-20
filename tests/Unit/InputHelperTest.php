<?php

namespace Tests\Unit;

use PBWebDev\CardanoPress\ISPO\InputHelper;
use PHPUnit\Framework\TestCase;

class InputHelperTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        $_POST = [];
    }

    public function test_returns_null_for_missing_key(): void
    {
        $_POST = [];

        $this->assertNull(InputHelper::sanitizePostString('stakeAddress'));
    }

    public function test_returns_null_for_array_value(): void
    {
        $_POST = ['stakeAddress' => ['injected']];

        $this->assertNull(InputHelper::sanitizePostString('stakeAddress'));
    }

    public function test_returns_sanitized_string(): void
    {
        $_POST = ['stakeAddress' => 'stake1uxyz123'];

        $this->assertSame('stake1uxyz123', InputHelper::sanitizePostString('stakeAddress'));
    }

    public function test_applies_wp_unslash(): void
    {
        $_POST = ['stakeAddress' => 'stake1\\\' test'];

        $result = InputHelper::sanitizePostString('stakeAddress');

        $this->assertNotNull($result);
        $this->assertStringNotContainsString('\\', $result);
    }
}
