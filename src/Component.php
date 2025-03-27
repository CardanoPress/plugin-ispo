<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

class Component
{
    protected Application $application;
    protected bool $echo;

    public function __construct(bool $echo)
    {
        $this->application = Application::getInstance();
        $this->echo = $echo;
    }

    public function cardanoPressISPO(): string
    {
        $ration = $this->application->option('rewards_ration');
        $minAda = $this->application->option('rewards_minimum');
        $maxAda = $this->application->option('rewards_maximum');
        $commence = $this->application->option('rewards_commence');
        $conclude = $this->application->option('rewards_conclude');

        $this->application->enqueue('script', Manifest::HANDLE_PREFIX . 'script');

        return $this->attributes([
            'x-data' => 'cardanoPressISPO',
            'data-ration' => $ration,
            'data-minimum' => $minAda,
            'data-maximum' => $maxAda,
            'data-commence' => $commence,
            'data-conclude' => $conclude,
        ]);
    }

    public function commenceUTC(): string
    {
        $commence = $this->application->option('rewards_commence');

        return $this->printOrReturn(Actions::toUTC($commence));
    }

    public function concludeUTC(): string
    {
        $conclude = $this->application->option('rewards_conclude');

        return $this->printOrReturn(Actions::toUTC($conclude));
    }

    public function allocatedTokens(): string
    {
        $value = number_format($this->application->option('allocated_tokens'));

        return $this->printOrReturn($value);
    }

    public function cardanoscanLink(string $endpoint): string
    {
        $network = $this->application->userProfile()->connectedNetwork();
        $value = Actions::getCardanoscanLink($network, $endpoint);

        return $this->printOrReturn(esc_url($value));
    }

    protected function attributes(array $data): string
    {
        $attr = [];

        foreach (array_filter($data) as $key => $value) {
            $attr[] = sprintf('%s="%s"', $key, esc_attr($value));
        }

        $attr = implode(' ', $attr);

        return $this->printOrReturn($attr);
    }

    protected function printOrReturn(string $value): string
    {
        if (! $this->echo) {
            return $value;
        }

        echo $value;

        return '';
    }
}
