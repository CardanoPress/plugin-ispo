<?php

/**
 * @package ThemePlate
 */

namespace Tests\Integration;

use PBWebDev\CardanoPress\ISPO\Application;
use PBWebDev\CardanoPress\ISPO\Component;
use PHPUnit\Framework\TestCase;
use Tests\LoadDependencies;

class ComponentTest extends TestCase
{
    use LoadDependencies;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadDependencies();

        if (! defined('CARDANOPRESS_FILE')) {
            define('CARDANOPRESS_FILE', dirname(__DIR__, 2) . '/cardanopress-ispo.php');
        }

        new Application(CARDANOPRESS_FILE);
    }

    public static function for_instance(): array
    {
        return array(
            array('cardanoPressISPO'),
        );
    }

    /** @dataProvider for_instance */
    public function test_echoed_instance(string $method): void
    {
        $component = new Component(true);

        ob_start();
        $component->{$method}();

        $this->assertStringContainsString('x-data="' . $method . '"', ob_get_clean());
    }

    /** @dataProvider for_instance */
    public function test_returned_instance(string $method): void
    {
        $component = new Component(false);

        $this->assertStringContainsString('x-data="' . $method . '"', $component->{$method}());
    }
}
