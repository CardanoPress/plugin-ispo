<?php

namespace Tests\Unit;

use PBWebDev\CardanoPress\ISPO\Manager;
use PBWebDev\CardanoPress\ISPO\Application;
use PHPUnit\Framework\TestCase;

class ManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 3) . '/cardanopress/dependencies/vendor/autoload_packages.php';
        require_once dirname(__DIR__, 3) . '/cardanopress/src/Blockfrost.php';

        if (! defined('CP_ISPO_FILE')) {
            define('CP_ISPO_FILE', dirname(__DIR__, 2) . '/cardanopress-ispo.php');
        }

        new class (CP_ISPO_FILE) extends Application {
            public function isReady(): bool
            {
                return true;
            }
        };

        add_filter('cp-ispo-max_pagination_pages', function () {
            return 1;
        });
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        remove_all_filters('cp-ispo-max_pagination_pages');
    }

    public function test_calculateRewards_respects_page_cap(): void
    {
        $requestCount = 0;
        $blockfrost = $this->createMock(\PBWebDev\CardanoPress\Blockfrost::class);
        $blockfrost->method('getAccountHistory')->willReturnCallback(
            function () use (&$requestCount) {
                $requestCount++;

                return array_fill(0, 100, [
                    'pool_id' => 'pool_nonexistent',
                    'active_epoch' => 300,
                ]);
            }
        );

        $reflection = new \ReflectionMethod(Manager::class, 'calculateRewards');
        $reflection->setAccessible(true);
        $reflection->invoke(null, $blockfrost, 'stake1test');

        $this->assertSame(1, $requestCount);
    }
}
