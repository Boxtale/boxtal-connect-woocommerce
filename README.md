Welcome to the <a href="https://www.boxtal.com/">Boxtal</a> repository on GitHub. Here you can browse the source, look at open issues and keep track of development.

If you are not a developer, please use the Boxtal plugin page on WordPress.org.

## Hooks

### Tracking event hook

You can use the tracking event hook to launch actions when a boxtal shipment tracking status changes.

`function my_tracking_function($order_id, $carrier_reference, $tracking_event_date, $tracking_event_code) {
 /* your code here */
}
add_action( 'boxtal_tracking_event', 'my_tracking_function' );`

Be aware that a WooCommerce can have several carrier references.

## Contributing to Boxtal WooCommerce
If you have a patch or have stumbled upon an issue with our plugin, you can contribute this back to the code. Please read our [contributor guidelines](https://github.com/woocommerce/woocommerce/blob/master/.github/CONTRIBUTING.md) for more information how you can do this.