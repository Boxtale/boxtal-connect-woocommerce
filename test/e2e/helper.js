import { WebDriverHelper as helper } from 'wp-e2e-webdriver';

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
        console.log('test0');
        options.forEach(function(item, index) {
            console.log('tests');
            /*if (0 === index) {
                driver.actions().keyDown( Key.CTRL ).perform();
            }*/
            promises.push(element.findElement(_seleniumWebdriver.By.xpath('.//option[contains(text(),"' + item + '")]')).then(
                (option) => {option.click().then(() => {console.log('tests2');return true;}, () => {return false;})},
                () => {return false;}
            ));
            /*if ((options.length - 1) === index) {
                driver.actions().keyUp( Key.CTRL ).perform();
            }*/
        });
        return Promise.all(promises).then(() => {
            console.log(values);
            return true;
        }, () => {return false;});
    }, () => {return false;} );
}
