=== Locked Payment Methods for WooCommerce ===
Contributors: ahegyes, deepwebsolutions
Tags: payment methods, woocommerce, manual permission
Requires at least: 5.5
Tested up to: 6.0.1
Requires PHP: 7.4
Stable tag: 1.3.5  
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

A WooCommerce extension which allows shop managers to hide payment methods from customers that haven't been manually granted access yet.

== Description ==

**Locked Payment Methods is a WooCommerce extension for hiding enabled payment methods from customers that haven't been granted access to them yet.**

A typical use-case involves locking high-risk payment methods like *Cash on delivery* or *Pay by invoice* but you can choose to lock any payment method supported by WooCommerce for any reason.

By locking them, these payment methods are automatically removed from the WooCommerce checkout page for any customer that hasn't been granted access. You can then choose to grant access on a case-by-case basis or based on other objective criteria for logged-in users that you trust. Everyone else will be forced to check out using any of the other payment methods that your store provides.

### Granting your customers access to a locked payment method

There are multiple pre-bundled ways of to grant a customer access to a locked payment method. They are all documented in our knowledge base:

* Grant access to each customer individually ([read more](https://docs.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/unlocking-payment-methods-for-specific-users/))
* Grant access to all customers belonging to given user roles ([read more](https://docs.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/unlocking-payment-methods-for-specific-user-roles/))
* **[Premium]** Grant access for only one unpaid order ([read more](https://docs.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/unlocking-payment-methods-for-specific-orders/))
* **[Premium]** Grant access to all users with an active [WC Memberships](https://woocommerce.com/products/woocommerce-memberships/) plan ([read more](https://docs.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/unlocking-payment-methods-for-specific-membership-plans/))
* **[Premium]** Grant access to all users belonging to a [Groups](https://wordpress.org/plugins/groups/) group ([read more](https://docs.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/unlocking-payment-methods-for-specific-groups/))

### Premium support and features

Some of the unlocking strategies mentioned above are only bundled with the premium version of our plugin available [here](https://www.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/). It is perfectly possible, however, to use the free version and extend it via filters and actions with your own lock/unlock strategies.

Premium customers are also entitled to prioritized help and support through [our support forum](https://www.deep-web-solutions.com/support/).

== Installation ==

This plugin requires WooCommerce 4.5+ to run. If you're running a lower version, please update first. After you made sure that you're running a supported version of WooCommerce, you may install `Locked Payment Methods for WooCommerce` either manually or through your site's plugins page.

### INSTALL FROM WITHIN WORDPRESS

1. Visit the plugins page withing your dashboard and select `Add New`.
1. Search for `Locked Payment Methods for WooCommerce` and click the `Install Now` button.

1. Activate the plugin from within your `Plugins` page.


### INSTALL MANUALLY

1. Download the plugin from https://wordpress.org/plugins/ and unzip the archive.
1. Upload the `locked-payment-methods-for-woocommerce` folder to the `/wp-content/plugins/` directory.

1. Activate the plugin through the `Plugins` menu in WordPress.


### AFTER ACTIVATION

If the minimum required version of WooCommerce is present, you will find a section present in the `Payments` tab of the WooCommerce `Settings` page. This section is called `Locked Payment Methods`. There you will be able to:

1. Choose which enabled payment methods are locked by default.
1. Choose which unlocking strategies you want to use and configure them, if applicable.

== Frequently Asked Questions ==

= What is a locked payment method? =

That's what we call payment methods that you choose to hide from your customers by default. The action of granting a customer access is called *unlocking* the payment method.

= Why can't I lock a certain payment method? =

Only enabled payment methods can be locked. Please make sure that the payment method you want to lock is first enabled in the WooCommerce settings.

= How can I get help if I'm stuck? =

If you're using the free version, you can find more examples in [our knowledge base](https://docs.deep-web-solutions.com/article-categories/locked-payment-methods-for-woocommerce/) and you can open a community support ticket here at [wordpress.org](https://wordpress.org/support/plugin/locked-payment-methods-for-woocommerce/). Our staff regularly goes through the community forum to try and help.

If you've purchased the premium version of the plugin [on our website](https://www.deep-web-solutions.com/plugins/locked-payment-methods-for-woocommerce/), you are entitled to a year of premium updates and access to [our prioritized support forum](https://www.deep-web-solutions.com/support/). You can use that to contact our support team directly if you have any questions.


= I have a question that is not listed here =

There is a chance that your question might be answered [in our knowledge base](https://docs.deep-web-solutions.com/article-categories/locked-payment-methods-for-woocommerce/). Otherwise feel free to reach out via our [contact form](https://www.deep-web-solutions.com/contact/).

== Screenshots ==

1. Plugin settings section within the WooCommerce settings.
2. Example of how to unlock payment methods for all users of given roles.
3. Example of how to unlock payment methods for a specific user.

== Changelog ==

= 1.3.5 (July 13th, 2022) =
* Tested up to WordPress 6.0.1.
* Tested up to WooCommerce 6.7.0.
* Updated Freemius SDK.

= 1.3.4 (March 20th, 2022) =
* Tested up to WooCommerce 6.3.
* Security: updated Freemius SDK.

= 1.3.3 (February 8th, 2022) =
* Test up to WordPress 5.9.
* Dev: updated DWS framework.

= 1.3.2 (January 14th, 2022) =
* Tested up to WooCommerce 6.1.
* Tested up to PHP 8.1.
* Better handling of framework initialization failure.
* Dev: updated DWS framework.

= 1.3.1 (December 2nd, 2021) =
* Tested with the latest version of WordPress.
* Performance: optimized PSR4 autoloader.
* Dev: hook tags can now be generated before the plugin is initialized.
* Dev: updated DWS Framework.

= 1.3.0 (November 15th, 2021) =
* Tested with the latest versions of WP and WC.
* Additional licensing terms for redistribution.
* Fixed readme.txt formatting.
* Dev: Updated DWS Framework.
* Dev: Updated dependencies namespace. If you have custom code, please check for compatibility.
* Dev: fixed CI/CD tests.

= 1.2.1 (September 26th, 2021) =
* Fixed text domain within the dependencies folder.
* Fixed PHP8 error on settings page.

= 1.2.0 (September 24th, 2021) =
* Tested with the latest version of WordPress and WooCommerce.
* Dev: updated the DWS framework.
* Dev: added more automated tests.



= 1.1.0 (July 26th, 2021) =
* Tested with latest versions of WooCommerce and WordPress.
* Dev: changed capabilities names to better reflect those registered by WordPress Core.
* Dev: bootstrap functions belong to the global namespace now.
* Dev: updated DWS plugin framework.

= 1.0.0 (May 29th, 2021) =
* First official release.
