<?php

/**
 * @package ThemePlate
 */

namespace Tests\Integration;

use PBWebDev\CardanoPress\ISPO\Application;
use PBWebDev\CardanoPress\ISPO\Exporter;
use Tests\LoadDependencies;

class ExporterTest extends \WP_UnitTestCase
{
    use LoadDependencies;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loadDependencies();

        if (! defined('CARDANOPRESS_FILE')) {
            define('CARDANOPRESS_FILE', dirname(__DIR__, 2) . '/cardanopress-ispo.php');
        }

        new class (CARDANOPRESS_FILE) extends Application {
            public function isReady(): bool
            {
                return true;
            }
        };
    }

    public function test_doAction_requires_manage_options(): void
    {
        $userId = self::factory()->user->create(['role' => 'subscriber']);

        if ($userId instanceof \WP_Error) {
            $this->fail($userId->get_error_message());
        }

        wp_set_current_user($userId);

        $_REQUEST['nonce'] = wp_create_nonce(Exporter::ACTION);
        $_GET['pool'] = 'test_pool';

        $this->expectException(\WPDieException::class);
        $this->expectExceptionCode(403);

        $exporter = new Exporter(Application::getInstance()->logger('exporter'));
        $exporter->doAction();
    }
}
