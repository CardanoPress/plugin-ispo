<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

use CardanoPress\Foundation\AbstractApplication;
use CardanoPress\Traits\Configurable;
use CardanoPress\Traits\Enqueueable;
use CardanoPress\Traits\Instantiable;
use CardanoPress\Traits\Templatable;

class Application extends AbstractApplication
{
    use Configurable;
    use Enqueueable;
    use Instantiable;
    use Templatable;

    protected PoolManager $poolManager;

    protected function initialize(): void
    {
        $this->setInstance($this);

        $path = plugin_dir_path($this->getPluginFile());
        $this->admin = new Admin($this->logger('admin'));
        $this->manifest = new Manifest($path . 'assets/dist', $this->getData('Version'));
        $this->templates = new Templates($path . 'templates');
        $this->poolManager = new PoolManager($this->logger('poolManager'));
    }

    public function setupHooks(): void
    {
        $this->admin->setupHooks();
        $this->manifest->setupHooks();
        $this->templates->setupHooks();

        add_action('cardanopress_loaded', [$this, 'init']);
    }

    public function init(): void
    {
        load_plugin_textdomain($this->getData('TextDomain'));

        (new Actions())->setupHooks();
        (new Shortcode())->setupHooks();
    }

    public function isReady(): bool
    {
        $function = function_exists('cardanoPress');
        $namespace = 'PBWebDev\\CardanoPress\\';
        $admin = class_exists($namespace . 'Admin');
        $blockfrost = class_exists($namespace . 'Blockfrost');

        return $function && $admin && $blockfrost;
    }

    public function userProfile(): Profile
    {
        static $user;

        if (null === $user) {
            $user = new Profile(wp_get_current_user());
        }

        return $user;
    }

    public function delegationPool(): array
    {
        static $data;

        if (null === $data) {
            $data = $this->poolManager->getData();
        }

        $showcase = get_post_meta(get_the_ID(), Admin::OPTION_KEY . '_pool_id', true);

        if (empty($showcase)) {
            $showcase = array_key_first($data);
        }

        if (! array_key_exists($showcase, $data)) {
            return $this->poolManager::DATA_STRUCTURE;
        }

        return $data[$showcase];
    }

    public function option(string $key)
    {
        $map = [
            'allocated_tokens' => 'allocated_tokens',
            'delegation_pool_id' => 'pool_id',
            'rewards_ration' => 'ration',
            'rewards_minimum' => 'minimum',
            'rewards_maximum' => 'maximum',
            'rewards_commence' => 'commence',
            'rewards_conclude' => 'conclude',
            'rewards_multiplier' => 'multiplier',
        ];

        if (in_array($key, array_keys($map), true)) {
            $settings = $this->admin->getOption('settings');

            return $settings[0][$map[$key]] ?? '';
        }

        return $this->admin->getOption($key);
    }
}
