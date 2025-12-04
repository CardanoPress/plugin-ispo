<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

use Exception;
use CardanoPress\Dependencies\Psr\Log\LoggerInterface;
use CardanoPress\Interfaces\HookInterface;
use CardanoPress\ISPO\Dependencies\League\Csv\Writer;
use CardanoPress\Traits\Loggable;
use PBWebDev\CardanoPress\Blockfrost;

/**
 * @phpstan-import-type SettingsStructure from Manager
 * @phpstan-type DelegationStructure array{
 *     stake_address: string,
 *     amount: int,
 * }
 */
class Exporter implements HookInterface
{
    use Loggable;

    public const ACTION = 'cp-ispo_exporter_action';

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    public function setupHooks(): void
    {
        add_action('wp_ajax_' . self::ACTION, [$this, 'doAction']);
    }

    public static function actionButton(string $network, string $pool): string
    {
        return sprintf(
            '<a href="%1$s" class="button" target="_blank">%2$s</a>',
            add_query_arg(
                [
                    'action' => self::ACTION,
                    'nonce' => wp_create_nonce(self::ACTION),
                    'network' => $network,
                    'pool' => $pool,
                ],
                admin_url('admin-ajax.php')
            ),
            __('Start Process', 'cardanopress-ispo')
        );
    }

    public function doAction(): void
    {
        if (! Application::getInstance()->isReady()) {
            $this->dieHandler('unready');
        }

        if (! wp_verify_nonce($_REQUEST['nonce'] ?? '', self::ACTION)) {
            $this->dieHandler('forbidden');
        }

        $pool = sanitize_key($_GET['pool'] ?? '');

        if (empty($pool) || ! in_array($pool, Manager::getPoolIDs(), true)) {
            $this->dieHandler('unknown');
        }

        try {
            $csv = Writer::createFromStream(tmpfile());
            $index = array_search($pool, Manager::getPoolIDs(), true);

            if (false === $index) {
                $this->dieHandler('unknown');
            }

            $settings = Manager::getSettings((int) $index);

            $this->log(__METHOD__ . ' ' . $pool);
            $csv->insertOne(array_keys($this->prepareHolder($settings)));
            $csv->insertAll($this->getData($settings));
            $csv->output(date('Y-m-d-H-i') . '-data.csv');
        } catch (Exception $exception) {
            $this->log($exception->getMessage());
        }

        $this->dieHandler('success');
    }

    protected function dieHandler(string $type): void
    {
        $types = [
            'success' => [
                'message' => '',
                'code' => 200,
            ],
            'unknown' => [
                'message' => __('Unknown pool to process', 'cardanopress-ispo'),
                'code' => 400,
            ],
            'forbidden' => [
                'message' => __('Invalid nonce value provided', 'cardanopress-ispo'),
                'code' => 403,
            ],
            'unready' => [
                'message' => __('Application not ready to process', 'cardanopress-ispo'),
                'code' => 409,
            ],
        ];

        wp_die($types[$type]['message'], __('Export Data', 'cardanopress-ispo'), $types[$type]['code']);
    }

    /**
     * @param SettingsStructure $settings
     * @return array{
     *     stake_address: string,
     *     total_amount: int,
     *     total_reward: int,
     * }
     */
    protected function prepareHolder(array $settings, string $stakeAddress = ''): array
    {
        $prepared = apply_filters(
            'cp-ispo-export_csv_headers',
            [
                'stake_address' => $stakeAddress,
                'total_amount' => 0,
                'total_reward' => 0,
            ],
            compact('settings', 'stakeAddress')
        );

        for ($i = $settings['commence']; $i <= $settings['conclude']; $i++) {
            $prepared['amount_' . $i] = 0;
            $prepared['reward_' . $i] = 0;
        }

        return $prepared;
    }

    /**
     * @param SettingsStructure $settings
     * @return array<string, DelegationStructure>
     */
    protected function getData(array $settings): array
    {
        $blockfrost = new Blockfrost($settings['network']);
        $delegations = [];

        for ($epoch = $settings['commence']; $epoch <= $settings['conclude']; $epoch++) {
            $page = 1;

            do {
                $response = $this->requestData($blockfrost, $settings['pool_id'], $epoch, $page);

                $this->log(__METHOD__ . ' count=' . count($response));

                foreach ($response as $delegation) {
                    $address = $delegation['stake_address'];
                    $amount = $delegation['amount'];
                    $reward = Manager::calculateReward($settings['ration'], $amount, $settings['multiplier']);

                    if (empty($delegations[$address])) {
                        $delegations[$address] = $this->prepareHolder($settings, $address);
                    }

                    $delegations[$address]['amount_' . $epoch] = $amount;
                    $delegations[$address]['reward_' . $epoch] = $reward;
                    $delegations[$address]['total_amount'] += $amount;
                    $delegations[$address]['total_reward'] += $reward;

                    do_action('cp-ispo-export_qualified_epoch', $epoch, $delegation, $settings);
                }

                $page++;
            } while (100 === count($response));
        }

        return apply_filters('cp-ispo-export_csv_data', $delegations, $settings);
    }

    /** @return DelegationStructure[]|array{} */
    protected function requestData(Blockfrost $blockfrost, string $pool_id, int $epoch, int $page): array
    {
        $this->log(__METHOD__ . ' epoch ' . $epoch . ' page #' . $page);

        $response = $blockfrost->request('epochs/' . $epoch . '/stakes/' . $pool_id, compact('page'));

        return 200 === $response['status_code'] ? $response['data'] : [];
    }
}
