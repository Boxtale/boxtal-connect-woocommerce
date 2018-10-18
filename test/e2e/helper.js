import { WebDriverHelper as helper } from 'wp-e2e-webdriver';
import { By } from 'selenium-webdriver';

/**
 * Wait for element, located by `selector`, until present and multiselection is applied. Timeout
 * occurs after `waitMs` if element located by `selector` is not present and
 * displayed.
 *
 * @param {WebDriver} driver   - Instance of WebDriver.
 * @param {object}    selector - Instance of locator, mechanism for locating an element
 *                               on the page. For example `By.css( '#content' )`.
 * @param {array}    options   - Text of options to be selected
 *
 * @return {Promise} A promise that will be resolved with `true` value if element
 *                   located `selector` is present and selection is successful, or rejected if
 *                   times out waiting element to present and displayed.
 */
export function multiselect( driver, selector, options ) {
    return helper.waitTillPresentAndDisplayed( driver, selector).then( (element) => {
        const promises = [];
        options.forEach(function(item, index) {
            let promise = driver.actions().keyDown('\uE009').click(driver.findElement(By.xpath('//option[contains(text(),"' + item + '")]'))).keyUp('\uE009').perform();
            promises.push(promise);
        });
        return Promise.all(promises).then(() => {
            return true;
        }, () => {return false;});
    }, () => {return false;} );
}
