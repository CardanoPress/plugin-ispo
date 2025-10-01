<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

use CardanoPress\Foundation\AbstractShortcode;
use CardanoPress\Traits\HasTemplateShortcodes;

class Shortcode extends AbstractShortcode
{
    use HasTemplateShortcodes;

    protected Application $application;
    protected Component $component;

    public function __construct()
    {
        $this->application = Application::getInstance();
        $this->component = new Component(false);
    }

    public function setupHooks(): void
    {
        add_shortcode('cp-ispo_component', [$this, 'doComponent']);
        add_shortcode('cp-ispo_template', [$this, 'doTemplate']);
        add_shortcode('cp-ispo_template_if', [$this, 'doTemplateIf']);
    }

    /** @param array<string, string> $attributes */
    public function doComponent(array $attributes, ?string $content = null): string
    {
        $html = '<div ' . $this->component->cardanoPressISPO() . '>';
        $html .= apply_filters('the_content', $content);
        $html .= '</div>';

        return trim($html);
    }
}
