<?php

defined('ABSPATH') or exit;

add_filter('map_meta_cap', static function ($caps, $cap, $user_id) {
    switch ($cap) {
        case 'update_plugins':
        case 'update_themes':
        case 'update_core':
            /* @phpstan-ignore phpstanWP.wpConstant.fetch */
            if (defined('DISALLOW_FILE_MODS') && DISALLOW_FILE_MODS) {
                  $caps = array_diff($caps, ['do_not_allow']);
                if (is_multisite() && ! is_super_admin($user_id)) {
                    $caps[] = 'do_not_allow';
                } else {
                    $caps[] = $cap;
                }
            }
            break;
        default:
            break;
    }
        return $caps;
}, 100, 3);

