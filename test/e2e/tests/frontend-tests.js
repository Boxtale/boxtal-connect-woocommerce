import config from 'config';
import chai from 'chai';
import chaiAsPromised from 'chai-as-promised';
import test from 'selenium-webdriver/testing';
import {WebDriverManager, WebDriverHelper as helper} from 'wp-e2e-webdriver';
import {StoreOwnerFlow, ShopPage, CartPage} from 'wc-e2e-page-objects';
import { By } from 'selenium-webdriver';

chai.use( chaiAsPromised );

const assert = chai.assert;

const storeOwnerFlowArgs = {
	baseUrl: config.get( 'url' ),
	username: config.get( 'users.admin.username' ),
	password: config.get( 'users.admin.password' )
};

let manager;
let driver;

test.describe(
	'Frontend Tests', function () {

		// Set up the driver and manager before testing starts.
		test.before(
			function () {
				this.timeout( config.get( 'startBrowserTimeoutMs' ) );

				manager = new WebDriverManager( 'chrome', { baseUrl: config.get( 'url' ) } );
				driver  = manager.getDriver();

				helper.clearCookiesAndDeleteLocalStorage( driver );

				const storeOwner = new StoreOwnerFlow( driver, storeOwnerFlowArgs );

				// General settings for this test.
				storeOwner.setGeneralSettings(
					{
						baseLocation: [ 'United States', 'United States (US) — California' ],
						sellingLocation: 'Sell to all countries',
						enableTaxes: true,
						currency: [ 'United States', 'United States dollar ($)' ],
					}
				);

				// Make sure payment method is set in setting.
				storeOwner.enableBACS();
				storeOwner.enableCOD();
				storeOwner.enablePayPal();

				storeOwner.logout();
			}
		);

		this.timeout( config.get( 'mochaTimeoutMs' ) );

		test.it(
			'Adds the product to the cart when "Add to cart" is clicked', () => {
            // Create a new Shop page object.
				const shopPage = new ShopPage( driver, { url: manager.getPageUrl( 'index.php/shop' ) } );
            // Add a couple products to the cart.
				// If you're not using the WooCommerce dummy data, use any simple products on your shop's first page.
				const added_beanie = shopPage.addProductToCart( 'Beanie with Logo' );
            const added_cap    = shopPage.addProductToCart( 'Cap' );
            // Verify products were added to cart successfully.
				// Page action methods return promises that evaluate to true if the action is performed successfully.
				assert.eventually.ok( added_beanie );
            assert.eventually.ok( added_cap );
            // Create a new Cart page object.
				const cartPage = new CartPage( driver, { url: manager.getPageUrl( 'index.php/cart' ) } );
            // Check the cart for the items added earlier.
				const has_beanie = cartPage.hasItem( 'Beanie with Logo' );
            const has_cap    = cartPage.hasItem( 'Cap' );
            // Verify the cart has the items.
				assert.eventually.ok( has_beanie );
            assert.eventually.ok( has_cap );
			}
		);

		// Close the browser after finished testing.
		test.after(
			() => {
            manager.quitBrowser();
			}
		);

	}
);
