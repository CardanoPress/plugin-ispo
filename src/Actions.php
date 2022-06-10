<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

use CardanoPress\Helpers\NumberHelper;
use CardanoPress\Helpers\WalletHelper;
use CardanoPress\Interfaces\HookInterface;
use PBWebDev\CardanoPress\Blockfrost;
use WP_User;

class Actions implements HookInterface
{
    public function setupHooks(): void
    {
        add_action('cardanopress_wallet_status_checks', [$this, 'getAccountDetails']);
        add_action('wp_ajax_nopriv_cp-ispo_track_rewards', [$this, 'getStakeRewards']);
        add_action('wp_ajax_cp-ispo_track_rewards', [$this, 'getStakeRewards']);
        add_action('wp_ajax_cp-ispo_delegation_data', [$this, 'getDelegationData']);
    }

    public function getAccountDetails(WP_User $user)
    {
        $userProfile = new Profile($user);
        $rewards = $this->calculateRewards($userProfile->connectedStake(), $userProfile->connectedNetwork());

        $userProfile->saveCalculatedRewards($rewards);
    }

    protected function calculateRewards(string $stakeAddress, string $queryNetwork): float
    {
        $application = Application::getInstance();
        $poolIds = $application->option('delegation_pool_id');
        $blockfrost = new Blockfrost($queryNetwork);
        $ration = $application->option('rewards_ration');
        $commence = $application->option('rewards_commence');
        $conclude = $application->option('rewards_conclude');
        $multiplier = $application->option('rewards_multiplier');
        $rewards = 0;
        $page = 1;

        do {
            $response = $blockfrost->getAccountHistory($stakeAddress, $page);

            foreach ($response as $history) {
                if ($history['pool_id'] !== $poolIds[$queryNetwork]) {
                    continue;
                }

                if ($history['active_epoch'] >= $commence && $history['active_epoch'] <= $conclude) {
                    $rewards += $ration / 100 * NumberHelper::lovelaceToAda($history['amount']) * $multiplier;
                }
            }

            $page++;
        } while (100 === count($response));

        if (0 === $rewards) {
            return $rewards;
        }

        $latest = $blockfrost->getEpochsLatest();

        if (empty($latest) || $latest['epoch'] > $conclude) {
            return $rewards;
        }

        $response = $blockfrost->getAccountDetails($stakeAddress);

        if (! empty($response) && $response['active'] && $response['pool_id'] === $poolIds[$queryNetwork]) {
            $rewards += $ration / 100 * NumberHelper::lovelaceToAda($response['controlled_amount']) * $multiplier;
        }

        return $rewards;
    }

    public function getStakeRewards(): void
    {
        check_ajax_referer('cardanopress-actions');

        if (empty($_POST['stakeAddress']) || ! Application::getInstance()->isReady()) {
            wp_send_json_error(__('Something is wrong. Please try again', 'cardanopress-ispo'));
        }

        $stakeAddress = $_POST['stakeAddress'];
        $queryNetwork = WalletHelper::getNetworkFromStake($stakeAddress);

        wp_send_json_success($this->calculateRewards($stakeAddress, $queryNetwork));
    }

    public function getDelegationData(): void
    {
        check_ajax_referer('cardanopress-actions');

        $poolData = Application::getInstance()->delegationPool();
        $response = $poolData['hex'] ?? '';

        if (empty($response)) {
            wp_send_json_error(__('Something is wrong. Please try again', 'cardanopress-ispo'));
        }

        wp_send_json_success($response);
    }
}
