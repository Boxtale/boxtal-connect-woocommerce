/**
 * @module WPAdminWCSettingsShippingZoneEdit
 */

/**
 * External dependencies
 */
import { By } from 'selenium-webdriver';
import { WebDriverHelper as helper } from 'wp-e2e-webdriver';
import * as wcHelper from 'wc-e2e-page-objects/lib/helper';
import { WPAdminWCSettings } from 'wc-e2e-page-objects';

const defaultArgs = {
    url: '',
    visit: true,
};

const ADD_SHIPPING_METHOD_SELECTOR = By.css( '.wc-shipping-zone-add-method' );
const SHIPPING_METHOD_SELECTOR = wcHelper.getSelect2ToggleSelectorByName( 'add_method_id' );
const VALIDATE_SHIPPING_METHOD_SELECTOR = By.id( 'btn-ok' );
const SHIPPING_METHOD_TITLE_SELECTOR = By.css( '.wc-shipping-zone-method-rows tr:last-child .wc-shipping-zone-method-title' );
const EDIT_LAST_SHIPPING_METHOD_SELECTOR = By.css( '.wc-shipping-zone-method-rows tr:last-child .row-actions .wc-shipping-zone-method-settings' );
const RATE_INPUT_SELECTOR = By.id( 'woocommerce_flat_rate_cost' );
const PARCEL_POINT_NETWORKS_SELECTOR = By.id( 'woocommerce_flat_rate_bw_parcel_point_networks' );

/**
 * The admin Shipping: Shipping Options screen
 *
 * @extends WPAdminWCSettings
 */
export default class WPAdminWCSettingsShippingZoneEdit extends WPAdminWCSettings {

    /**
     * @param {WebDriver} driver   - Instance of WebDriver.
     * @param {object}    args     - Configuration arguments.
     */
    constructor( driver, args = {} ) {
        args = Object.assign( defaultArgs, args );
        super( driver, args );
    }

    /**
     * Add shipping method.
     *
     * @param string shipping method name.
     * @return {Promise}   Promise that evaluates to `true` if flat rate is successfully added, `false` otherwise.
     */
    addShippingMethod(name) {
        const self = this;
        return self.driver.findElement( ADD_SHIPPING_METHOD_SELECTOR ).then( ( el ) => {
            return el.click().then( () => {
                return helper.selectOption( self.driver, SHIPPING_METHOD_SELECTOR, name ).then( () => {
                        return self.driver.findElement( VALIDATE_SHIPPING_METHOD_SELECTOR ).then( (el2) => {
                            return el2.click().then( () => {
                                return true;
                            }, () => {
                                return false;
                            } );
                        }, () => {
                            return false;
                        });
                    }, () => {
                    return false;
                });
            }, () => {
                return false;
            } );
        }, () => {
            return false;
        } );
    }

    /**
     * Add flat rate.
     *
     * @param float rate to be set.
     * @param array parcel point networks to be set.
     * @return {Promise}   Promise that evaluates to `true` if flat rate is successfully added, `false` otherwise.
     */
    addFlatRate(rate, parcelPointNetworks) {
        return this.addShippingMethod('Flat rate').then(() => this.editFlatRate(rate, parcelPointNetworks));
    }

    /**
     * Edit flat rate.
     *
     * @param string rate to be set.
     * @param string parcel point networks to be set.
     * @return {Promise}   Promise that evaluates to `true` if flat rate is successfully edited, `false` otherwise.
     */
    editFlatRate(rate, parcelPointNetworks) {
        const self = this;
        return helper.mouseMoveTo( self.driver, SHIPPING_METHOD_TITLE_SELECTOR ).then( () => {
            return self.driver.findElement( EDIT_LAST_SHIPPING_METHOD_SELECTOR ).then( (button1) => {
                return button1.click().then( () => {
                    return helper.setWhenSettable( self.driver, RATE_INPUT_SELECTOR, rate ).then( () => {
                        if (typeof parcelPointNetworks == "undefined") {
                            return self.driver.findElement( VALIDATE_SHIPPING_METHOD_SELECTOR ).then( (button2) => {
                                return button2.click().then( () => {
                                    return true;
                                }, () => {
                                    return false;
                                } );
                            });
                        }
                        return helper.selectOption( self.driver, PARCEL_POINT_NETWORKS_SELECTOR, parcelPointNetworks ).then( () => {
                            return self.driver.findElement( VALIDATE_SHIPPING_METHOD_SELECTOR ).then( (button2) => {
                                return button2.click().then( () => {
                                    return true;
                                }, () => {
                                    return false;
                                } );
                            });
                            }, () => {
                            return false;
                        });
                        }, () => {
                        return false;
                    });
                }, () => {
                    return false;
                } );
            }, () => {
                return false;
            } );
        }, () => {
            return false;
        });
    }
}
