Welcome to the <a href="https://www.boxtal.com/">Boxtal Connect</a> for WooCommerce repository on GitHub. Here you can browse the source, look at open issues and keep track of development.

If you are not a developer, please use the <a href="https://wordpress.org/plugins/boxtal-connect/">Boxtal Connect plugin page</a> on WordPress.org.

## Hooks

### Order tracking event hooks

You can use the order tracking event hooks to launch actions when you've sent shipment(s) via Boxtal and those shipments are shipped or delivered.

```
function my_order_shipped_function($order_id) {
    /* your code here */
}

function my_order_delivered_function($order_id) {
    /* your code here */
}

add_action( 'boxtal_connect_order_shipped', 'my_order_shipped_function' );`
add_action( 'boxtal_connect_order_delivered', 'my_order_delivered_function' );`
```

## Contributing to Boxtal Connect
If you have a patch or have stumbled upon an issue with our plugin, you can contribute this back to the code. Please read our [contributor guidelines](https://github.com/Boxtale/boxtal-connect-woocommerce/blob/master/.github/CONTRIBUTING.md) for more information how you can do this.
