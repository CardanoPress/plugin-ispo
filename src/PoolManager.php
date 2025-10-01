<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

use CardanoPress\Traits\Instantiable;
use CardanoPress\Traits\Loggable;
use PBWebDev\CardanoPress\Blockfrost;
use CardanoPress\Dependencies\Psr\Log\LoggerInterface;
use CardanoPress\Dependencies\ThemePlate\Cache\CacheManager;
use CardanoPress\Dependencies\ThemePlate\Process\Tasks;

/**
 * @phpstan-type DataStructure array{
 *     pool_id: string,
 *     hex: string,
 *     vrf_key: string,
 *     blocks_minted: int,
 *     blocks_epoch: int,
 *     live_stake: int,
 *     live_size: float,
 *     live_saturation: float,
 *     live_delegators: int,
 *     active_stake: int,
 *     active_size: float,
 *     declared_pledge: int,
 *     live_pledge: int,
 *     margin_cost: float,
 *     fixed_cost: int,
 *     reward_account: string,
 *     owners: array<string>,
 *     registration: array<string>,
 *     retirement: array<string>,
 *     url: string,
 *     hash: string,
 *     ticker: string,
 *     name: string,
 *     description: string,
 *     homepage: string,
 * }
 */
class PoolManager
{
    use Instantiable;
    use Loggable;

    protected Tasks $tasks;
    protected CacheManager $cache;

    public const IDENTIFIER = 'cp-ispo';
    public const EXPIRATION = 15; // in minutes

    public const DATA_STRUCTURE = [
        'pool_id' => '',
        'hex' => '',
        'vrf_key' => '',
        'blocks_minted' => 0,
        'blocks_epoch' => 0,
        'live_stake' => 0,
        'live_size' => 0.0,
        'live_saturation' => 0.0,
        'live_delegators' => 0,
        'active_stake' => 0,
        'active_size' => 0.0,
        'declared_pledge' => 0,
        'live_pledge' => 0,
        'margin_cost' => 0.0,
        'fixed_cost' => 0,
        'reward_account' => '',
        'owners' => [''],
        'registration' => [''],
        'retirement' => [''],
        'url' => '',
        'hash' => '',
        'ticker' => '',
        'name' => '',
        'description' => '',
        'homepage' => '',
    ];

    public function __construct(LoggerInterface $logger)
    {
        $this->setInstance($this);
        $this->setLogger($logger);

        $tasks = new Tasks(self::IDENTIFIER);
        $this->cache = new CacheManager($tasks);

        $tasks->report([$this->getLogger(), 'info']);
    }

    /** @return ?array<string, DataStructure> */
    public function getData(): ?array
    {
        return $this->cache->remember(
            self::IDENTIFIER . '-pool-data',
            [self::class, 'updateData'],
            self::EXPIRATION * MINUTE_IN_SECONDS
        );
    }

    /** @return ?array<string, DataStructure> */
    public static function updateData(): ?array
    {
        $application = Application::getInstance();

        if (! $application->isReady()) {
            return null;
        }

        $poolData = [];
        $keys = cardanoPress()->option('blockfrost_project_id');

        Blockfrost::useProjectIds($keys['mainnet'] ?? '', $keys['testnet'] ?? '');

        foreach ((array) $application->option('settings') as $setting) {
            $poolId = $setting['pool_id'];
            $queryNetwork = $setting['network'] ?? 'mainnet';

            if (empty($poolId)) {
                continue;
            }

            if (!Blockfrost::isReady($queryNetwork)) {
                $poolData[$poolId] = [];

                continue;
            }

            $blockfrost = new Blockfrost($queryNetwork);
            $information = $blockfrost->getPoolInfo($poolId);
            $metaData = $blockfrost->getPoolDetails($poolId);
            $poolData[$poolId] = array_merge($information, $metaData);
        }

        if (empty($poolData)) {
            return null;
        }

        /** @var array<string, DataStructure> */
        return $poolData;
    }
}
