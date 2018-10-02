/**
 * External dependencies
 */
import { By } from 'selenium-webdriver';
import { WebDriverHelper as helper } from 'wp-e2e-webdriver';
import {ComponentCheckout} from "wc-e2e-page-objects";

const SELECTOR = '#shipping_method';
const LAST_SHIPPING_METHOD_SELECTOR = By.css( '#shipping_method li:last-child input[type="radio"]' );
const PARCEL_POINT_LINK_SELECTOR = By.css( '.bw-select-parcel' );
const PARCEL_POINT_MAP_SELECTOR = By.id( 'bw-map-container' );
const FIRST_PARCEL_POINT_SELECTOR = By.css( '#bw-pp-container tr:first-child a.bw-parcel-point-button' );

export default class ComponentCheckoutShippingMethods extends ComponentCheckout {
    constructor( driver ) {
        super( driver, SELECTOR );
    }

    checkLastShippingMethod() {
        return helper.setCheckbox( this.driver, LAST_SHIPPING_METHOD_SELECTOR );
    }

    isDisplayedParcelPointLink() {
        return this.driver.findElement(PARCEL_POINT_LINK_SELECTOR);
    }

    selectParcelPoint() {
        const self = this;
        return self.driver.findElement(PARCEL_POINT_LINK_SELECTOR).then((el) => {
            console.log("success1");
            return el.click().then(
                () => {
                    console.log("success2");
                    return helper.waitTillPresentAndDisplayed(self.driver, PARCEL_POINT_MAP_SELECTOR).then(
                        () => {
                            console.log("success3");
                            return helper.waitTillPresentAndDisplayed(self.driver, FIRST_PARCEL_POINT_SELECTOR).then(
                                () => {
                                    console.log("success4");
                                    return self.driver.findElement(FIRST_PARCEL_POINT_SELECTOR).then(
                                        (parcelPointButton) => {
                                            console.log("success5");
                                            return parcelPointButton.click().then(
                                                () => {console.log("success6");return true;},
                                                () => {console.log("failure6");return false;});
                                        }, () => {console.log("failure5");return false;});
                                }, () => {console.log("failure4");return false;});
                        }, () => {console.log("failure3");return false;});
                }, () => {console.log("failure2");return false;});
        }, () => {console.log("failure1");return false;});
    }
}
