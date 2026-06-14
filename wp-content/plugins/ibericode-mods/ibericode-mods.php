<?php

/*
Plugin Name: ibericode mods
Description: a collection of commonly needed WordPresss modifications or enhancements
Author: Danny van Kooten
Version: 1.0.1
Author URI: https://www.dannyvankooten.com/
Requires at least: 6.5
Requires PHP: 8.3 
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Update URI: https://www.ibericode.com/
*/

if (PHP_VERSION_ID < 80300) {
    return;
}

if (! defined('ABSPATH')) {
    return;
}

$mods = apply_filters('ibericode_mods', ['cache', 'comments', 'email', 'misc', 'security', 'seo', 'uploads']);
foreach ($mods as $f) {
    require __DIR__ . "/includes/{$f}.php";
}