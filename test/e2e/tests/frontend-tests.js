import config from 'config';
import chai from 'chai';
import chaiAsPromised from 'chai-as-promised';
import test from 'selenium-webdriver/testing';
import {WebDriverManager, WebDriverHelper as helper} from 'wp-e2e-webdriver';
import {
    Helper, PageMap, StoreOwnerFlow, WPAdminProductNew, GuestCustomerFlow,
    CheckoutOrderReceivedPage
} from 'wc-e2e-page-objects';
import { By } from 'selenium-webdriver';
import WPAdminWCSettingsShippingZoneEdit from "../pages/wp-admin-wc-settings-shipping-zone-edit";
import ComponentCheckoutShippingMethods from "../components/component-checkout-shipping-methods";

chai.use( chaiAsPromised );

const assert = chai.assert;

const PAGE = PageMap.PAGE;
const PARCEL_POINT_ALERT_SELECTOR = By.css( 'ul.woocommerce-error' );
const storeOwnerFlowArgs = {
	baseUrl: config.get( 'url' ),
	username: config.get( 'users.admin.username' ),
	password: config.get( 'users.admin.password' )
};

let manager;
let driver;

test.describe(
	'Frontend', function () {

		// Set up the driver and manager before testing starts.
		test.before(
			function () {
				this.timeout( config.get( 'startBrowserTimeoutMs' ) );

				manager = new WebDriverManager( 'chrome', { baseUrl: config.get( 'url' ) } );
				driver  = manager.getDriver();

				helper.clearCookiesAndDeleteLocalStorage( driver );

				const storeOwner = new StoreOwnerFlow( driver, storeOwnerFlowArgs );

				/* add product */
                const product = new WPAdminProductNew( driver, { url: manager.getPageUrl( '/wp-admin/post-new.php?post_type=product' ) } );
                product.setTitle( 'BW test product' );

                const productData = product.components.metaBoxProductData;
                productData.selectProductType( 'Simple product' );

                const panelGeneral = productData.clickTab( 'General' );
                panelGeneral.setRegularPrice( '9,99' );

                product.publish();
                assert.eventually.ok( product.hasNotice( 'Product published.' ) );
				/* end of add product */

				/* configure shipping methods */
				const shippingZone = new WPAdminWCSettingsShippingZoneEdit( driver, { url: manager.getPageUrl( '/wp-admin/admin.php?page=wc-settings&tab=shipping&zone_id=0' ) });
                shippingZone.addFlatRate("shipping method 1", "0", []).then(() => {shippingZone.addFlatRate("shipping method 2", "5", ["Chronopost", "Relais colis"]);});
				/* end of configure shipping methods */

				storeOwner.logout();
			}
		);

		this.timeout( config.get( 'mochaTimeoutMs' ) );

		test.it(
			'Payment attempt with parcel point selection', () => {
                const guest = new GuestCustomerFlow( driver, { baseUrl: config.get( 'url' ) } );
                guest.fromShopAddProductsToCart( 'BW test product' );

                const checkoutPage = guest.open( PAGE.CHECKOUT );
                assert.eventually.ok( Helper.waitTillUIBlockNotPresent( driver ) );

                const billingDetails = checkoutPage.components.billingDetails;
                assert.eventually.ok( billingDetails.setFirstName( 'Jon' ) );
                assert.eventually.ok( billingDetails.setLastName( 'Snow' ) );
                assert.eventually.ok( billingDetails.setEmail( 'jon.snow@got.com' ) );
                assert.eventually.ok( billingDetails.setPhone( '123456789' ) );
                assert.eventually.ok( billingDetails.selectCountry( 'france', 'France' ) );
                assert.eventually.ok( billingDetails.setAddress1( '4 boulevard des Capucines' ) );
                assert.eventually.ok( billingDetails.setCity( 'Paris' ) );
                assert.eventually.ok( billingDetails.setZip( '75009' ) );

                const shippingMethods = new ComponentCheckoutShippingMethods(driver);
                assert.eventually.ok( shippingMethods.checkLastShippingMethod() );
                assert.eventually.ok( shippingMethods.isDisplayedParcelPointLink() );
                assert.eventually.ok( shippingMethods.selectParcelPoint() );

                checkoutPage.selectPaymentMethod( 'Check payments' );
                checkoutPage.placeOrder();
                Helper.waitTillUIBlockNotPresent( driver );

                const orderReceivedPage = new CheckoutOrderReceivedPage( driver, { visit: false } );

                assert.eventually.ok(
                    orderReceivedPage.hasText( 'Order received' )
                );
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
