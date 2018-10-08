#!/usr/bin/env bash

code="
function tracking_hook_tester(\$order_id, \$carrier_reference, \$tracking_event_date, \$tracking_event_code) {
\$order = wc_get_order(\$order_id);
\$note = 'order_id '.\$order_id.' carrier_reference '.\$carrier_reference;
\$note .= ' date '.\$tracking_event_date.' code '.\$tracking_event_code;
\$order->add_order_note(\$note);
\$order->save();
}
add_action('boxtal_tracking_event', 'tracking_hook_tester', 10, 4);
"

echo $code >> /var/www/html/wp-content/themes/storefront/functions.php
