---
title: "Data type inconsistencies in WordPress"
description: "Exploring data type inconsistencies in WordPress and the pitfalls of PHP's weak typing."
date: "2014-10-02T00:00:00+00:00"
modified: "2026-05-14T14:02:49+02:00"
categories: ["WordPress"]
---

WordPress plugin APIs become harder to use when a function can return different types for the same kind of request.

If a function sometimes returns an array, sometimes a string, and sometimes false, every caller has to defend against all three cases. That makes the code noisy, and it makes bugs more likely when one branch is forgotten.

```
function get_plugin_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return false;
    }

    $settings = get_option( 'my_plugin_settings' );

    if ( empty( $settings ) ) {
        return '';
    }

    return $settings;
}
```

This function looks harmless, but it has no clear contract. It can return a boolean, a string, or an array. Any code using it now has to know about all three possibilities.

A function should have a clear return contract: one type for success, and one documented type for failure.

A better version of the previous function would be the following.

```
function get_plugin_settings() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'forbidden', 'You are not allowed to read plugin settings.' );
    }

    $settings = get_option( 'my_plugin_settings', array() );

    return is_array( $settings ) ? $settings : array();
}
```

This function either returns an array or an instance of WP\_Error with context about the error that occurred. Much easier to use and reason about.

Good plugin APIs are boring and predictable. A caller should not have to inspect every possible return type before using a value.

Pick a return contract, document it, and keep filters constrained to that same contract. Future you, other developers, and anyone debugging a production warning will thank you.
