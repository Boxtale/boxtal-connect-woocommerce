#!/usr/bin/env bash

code="
function tracking_event_hook_tester(\$order_id) {
\$order = wc_get_order(\$order_id);
if ( false !== \$order ) {
\$note = 'order tracking event hook tester works!';
\$order->add_order_note(\$note);
\$order->save();
}
}
add_action('boxtal_connect_order_shipped', 'tracking_event_hook_tester');
add_action('boxtal_connect_order_delivered', 'tracking_event_hook_tester');
"

echo $code >> /var/www/html/wp-content/themes/storefront/functions.php
