<?php

/**
 * @package ThemePlate
 */

namespace Tests\Integration;

use PBWebDev\CardanoPress\ISPO\Actions;
use PBWebDev\CardanoPress\ISPO\Application;
use Tests\LoadDependencies;

class ActionsTest extends \WP_UnitTestCase
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

        add_filter('wp_doing_ajax', '__return_true');
        add_filter('wp_die_ajax_handler', [$this, 'get_wp_die_handler']);
    }

    protected function tearDown(): void
    {
        remove_filter('wp_doing_ajax', '__return_true');
        remove_filter('wp_die_ajax_handler', [$this, 'get_wp_die_handler']);

        parent::tearDown();
    }

    public function test_getStakeRewards_rejects_array_input(): void
    {
        $_POST['stakeAddress'] = ['injected'];
        $_POST['_wpnonce'] = wp_create_nonce('cardanopress-actions');
        $_REQUEST['_wpnonce'] = $_POST['_wpnonce'];

        $this->expectException(\WPDieException::class);

        $actions = new Actions();
        $actions->getStakeRewards();
    }
}
