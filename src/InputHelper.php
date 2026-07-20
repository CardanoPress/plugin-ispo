<?php

/**
 * @package ThemePlate
 * @since   1.12.0
 */

namespace PBWebDev\CardanoPress\ISPO;

class InputHelper
{
    public static function sanitizePostString(string $key): ?string
    {
        $value = $_POST[$key] ?? null;

        if (! is_string($value)) {
            return null;
        }

        $value = wp_unslash($value);

        if (! is_string($value)) {
            return null;
        }

        return sanitize_text_field($value);
    }
}
