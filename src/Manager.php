<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

use CardanoPress\Helpers\NumberHelper;
use PBWebDev\CardanoPress\Blockfrost;

/**
 * @phpstan-type SettingsStructure array{
 *     pool_id: string,
 *     network: string,
 *     allocated_tokens: int,
 *     ration: float,
 *     minimum: int,
 *     maximum: int,
 *     commence: int,
 *     conclude: int,
 *     multiplier: float,
 * }
 */
class Manager
{
    public const DATA_STRUCTURE = [
        'pool_id' => '',
        'network' => '',
        'allocated_tokens' => 0,
        'ration' => 0,
        'minimum' => 0,
        'maximum' => 0,
        'commence' => 0,
        'conclude' => 0,
        'multiplier' => 0,
    ];

    /** @return ($index is null ? SettingsStructure[] : SettingsStructure) */
    public static function getSettings(?int $index = null): array
    {
        $settings = Application::getInstance()->option('settings');

        if (null === $index) {
            return $settings ?: self::DATA_STRUCTURE;
        }

        return $settings[$index] ?: self::DATA_STRUCTURE;
    }

    /** @return string[] */
    public static function getPoolIDs(): array
    {
        return array_column(self::getSettings(), 'pool_id');
    }

    public static function calculateReward(float $ration, int $lovelace, float $multiplier): float
    {
        return $ration / 100 * NumberHelper::lovelaceToAda($lovelace) * $multiplier;
    }

    protected static function calculateRewards(Blockfrost $blockfrost, string $stakeAddress): float
    {
        $rewards = 0.0;
        $page = 1;

        do {
            $response = $blockfrost->getAccountHistory($stakeAddress, $page);

            foreach ($response as $history) {
                $index = array_search($history['pool_id'], self::getPoolIDs(), true);

                if (false === $index) {
                    continue;
                }

                $settings = self::getSettings((int) $index);
                $ration = $settings['ration'];
                $multiplier = $settings['multiplier'];
                $commence = $settings['commence'];
                $conclude = $settings['conclude'];

                if ($history['active_epoch'] >= $commence && $history['active_epoch'] <= $conclude) {
                    do_action('cp-ispo-qualified_epoch_for_rewards', $history['active_epoch'], $stakeAddress, $history);

                    $lovelace = (int) $history['amount'];
                    $rewards += apply_filters(
                        'cp-ispo-epoch_calculated_reward',
                        self::calculateReward($ration, $lovelace, $multiplier),
                        $history['active_epoch'],
                        compact('ration', 'lovelace', 'multiplier', 'rewards', 'history', 'stakeAddress'),
                    );
                }
            }

            $page++;
        } while (100 === count($response));

        return $rewards;
    }

    public static function getRewards(string $stakeAddress, string $queryNetwork): float
    {
        $blockfrost = new Blockfrost($queryNetwork);
        $poolIds = self::getPoolIDs();
        $account = $blockfrost->getAccountDetails($stakeAddress);
        $ration = 1;
        $multiplier = 1;
        $conclude = 0;

        if (! empty($account) && $account['active']) {
            $index = array_search($account['pool_id'], $poolIds, true);

            if (false !== $index) {
                $settings = self::getSettings((int) $index);
                $ration = $settings['ration'];
                $multiplier = $settings['multiplier'];
                $conclude = $settings['conclude'];
            }
        }

        $customRewards = apply_filters(
            'cp-ispo-force_wallet_rewards',
            null,
            $stakeAddress,
            compact('ration', 'multiplier')
        );

        if (null !== $customRewards) {
            return $customRewards;
        }

        $rewards = self::calculateRewards($blockfrost, $stakeAddress);

        if (0.0 === $rewards) {
            return apply_filters(
                'cp-ispo-total_accumulated_rewards',
                $rewards,
                compact('ration', 'multiplier', 'stakeAddress')
            );
        }

        $latest = $blockfrost->getEpochsLatest();

        if (empty($latest) || $latest['epoch'] > $conclude) {
            return apply_filters(
                'cp-ispo-total_accumulated_rewards',
                $rewards,
                compact('ration', 'multiplier', 'stakeAddress')
            );
        }

        if (! empty($account) && $account['active'] && in_array($account['pool_id'], $poolIds, true)) {
            do_action('cp-ispo-qualified_epoch_for_rewards', $latest['epoch'], $stakeAddress, $account);

            $lovelace = (int) $account['controlled_amount'];
            $rewards += apply_filters(
                'cp-ispo-epoch_calculated_reward',
                self::calculateReward($ration, $lovelace, $multiplier),
                $latest['epoch'],
                compact('ration', 'lovelace', 'multiplier', 'rewards', 'account', 'stakeAddress'),
            );
        }

        return apply_filters(
            'cp-ispo-total_accumulated_rewards',
            $rewards,
            compact('ration', 'multiplier', 'stakeAddress')
        );
    }
}
