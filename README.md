Welcome to the <a href="https://www.boxtal.com/">Boxtal Connect</a> for WooCommerce repository on GitHub. Here you can browse the source, look at open issues and keep track of development.

If you are not a developer, please use the <a href="https://wordpress.org/plugins/boxtal-connect/">Boxtal Connect plugin page</a> on WordPress.org.

## Hooks

### Tracking event hook

You can use the tracking event hook to launch actions when a shipment (sent via Boxtal) tracking status changes.

```
function my_tracking_function($order_id, $carrier_reference, $tracking_event_date, $tracking_event_code) {
    /* your code here */
}

add_action( 'boxtal_tracking_event', 'my_tracking_function' );`
```

Be aware that an order can have several carrier references if dispatched in several shipments.

Here is a list of all possible tracking event codes:
A - Waiting for pickup from carrier
B - Waiting for drop-off at proximity point
C - Shipping in progress
D - Exception
E - Delivery attempt
F - Delivered
G - Delivered at relay point
Z - Other
R - Returned
N - new

## Contributing to Boxtal Connect
If you have a patch or have stumbled upon an issue with our plugin, you can contribute this back to the code. Please read our [contributor guidelines](https://github.com/Boxtale/boxtal-connect-woocommerce/blob/master/.github/CONTRIBUTING.md) for more information how you can do this.
