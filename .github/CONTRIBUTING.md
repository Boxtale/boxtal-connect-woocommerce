# Contributing

Community made patches, bug reports and contributions are welcome on this project.

When contributing please ensure you follow the guidelines below to help us keep on top of things.

__Please Note:__

GitHub is for _bug reports and contributions only_ - if you have a support question or a request for a customization this is not the right place to post it.

## Reporting issues

If something isn't working, congratulations you've found a bug! Help us fix it by submitting an issue report:

* Make sure you have a [GitHub account](https://github.com/signup/free)
* Search the [Existing Issues](https://github.com/Boxtale/boxtal-woocommerce-poc/issues) to be sure that the one you've noticed isn't already there
* Submit a report for your issue
  * Clearly describe the issue (including steps to reproduce it if it's a bug)
  * Make sure you fill in the earliest version that you know has the issue
  * Give us your PHP, Wordpress and WooCommerce versions
  * Be sure to mention any plugin (with version) which you have installed and you suspect might be related to the issue

## Submitting a pull request

* Fork the repository on GitHub
* When committing, reference your issue number (#1234) and include a note about the fix
* Push the changes to your fork and submit a pull request on the master branch of the WooCommerce repository. Existing maintenance branches will be maintained by Boxtal WooCommerce developers
* Please **don't** add your localizations or update the .pot files - these will also be maintained by the Boxtal WooCommerce developers. To contribute to the localization of Boxtal WooCommerce, please join the [translate.wordpress.org project](https://translate.wordpress.org/projects/wp-plugins/boxtal-woocommerce).

After you follow the step above, the next stage will be waiting on us to merge your Pull Request. We review them all, and make suggestions and changes as and if necessary.

## Coding guidelines

### Plugin base structure

Our plugin base structure follows the [Plugin Container pattern](https://www.smashingmagazine.com/2015/05/how-to-use-autoloading-and-a-plugin-container-in-wordpress-plugins/)