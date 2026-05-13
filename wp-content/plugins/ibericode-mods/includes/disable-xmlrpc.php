<?php

// Prevent direct file access
defined('ABSPATH') or exit;

add_filter('xmlrpc_enabled', '__return_false');