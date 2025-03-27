<?php

/**
 * @package ThemePlate
 * @since   0.1.0
 */

namespace PBWebDev\CardanoPress\ISPO;

use CardanoPress\Foundation\AbstractShortcode;

class Shortcode extends AbstractShortcode
{
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
    }

    public function doComponent($attributes, ?string $content = null): string
    {
        $html = '<div ' . $this->component->cardanoPressISPO() . '>';
        $html .= apply_filters('the_content', $content);
        $html .= '</div>';

        return trim($html);
    }

    public function doTemplate(array $attributes): string
    {
        $args = shortcode_atts([
            'name' => '',
            'variables' => [],
            'if' => '',
        ], $attributes);

        if (empty($args['name'])) {
            return '';
        }

        if (isset($attributes['variables'])) {
            parse_str(str_replace('&amp;', '&', $args['variables']), $args['variables']);
        }

        ob_start();
        $this->application->template($args['name'], $args['variables']);

        $html = ob_get_clean();

        if (empty($args['if'])) {
            return $html;
        }

        return '<template x-if="' . $args['if'] . '">' . $html . '</template>';
    }
}
