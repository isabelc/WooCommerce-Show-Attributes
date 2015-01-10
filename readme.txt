=== WooCommerce Show Attributes ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: custom product attributes, woocommerce product attributes, product attributes, custom attributes, woocommerce custom product attributes
Requires at least: 3.8
Tested up to: 4.1
Stable tag: 1.3.1
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show WooCommerce custom product attributes on the Product, Shop, and Cart pages, admin Order Details page and emails.

== Description ==

This is an extension for WooCommerce that will show your custom product attributes on the single product page above the "Add to cart" button instead of in the "Additional Information" tab. This does NOT affect nor include attributes which are used for Variations.

The custom product product attributes will **also** be displayed on:

* Cart page
* View Order page on front end for customers
* Edit Order admin page, under Order Items
* New Order email that goes to the administrator
* Receipt (Order Processing) email that goes to the customer
* Order Complete email that goes to the customer
* Optional: Shop page (including product category and tag archives)

Includes a .pot localization file to make this plugin WPML-ready.

See the [documentation](http://isabelcastillo.com/docs/category/woocommerce-show-attributes).

Fork it [on Github](https://github.com/isabelc/WooCommerce-Show-Attributes).

== Installation ==

1.  Download the plugin `.zip` file.

2.  Go to **Plugins -> Add New -> Upload** to upload the `.zip` file.

3.  Click "Activate" to activate the plugin.

4. For each attribute that you want to display, you must check the box for “Visible on the product page.” This has to be checked for the attributes to appear on the single product page and Shop page.

This is a WooCommerce native option and is found on the Edit Product page, under the individual attribute settings.

If you uncheck that box, the attribute will not be shown on the single product page or Shop page. However, it will still show up on the rest of the places as described in the plugin description.

5.  Optional settings are at WooCommerce Settings -> Product tab, under "Product Data".


== Frequently Asked Questions ==

= How do I hide the attribute labels and only show the values? = 

Go to WooCommerce Settings -> Product tab, under "Product Data". Check the box for **"Hide the Labels When Showing Product Attributes"**. Click "Save changes". 


= How do I remove the list bullets from the attributes on the single product page? = 

Go to WooCommerce Settings -> Product tab, under "Product Data". Check the box for **"Show Attributes in a span Element"**. Click "Save changes".  


= How can I style the attributes? = 

This plugin adds several CSS selectors so that you can style the output by adding your own CSS.

On the "single product page", the attributes are in an unordered list with the CSS class "custom-attributes". Each list item has two CSS classes: its attribute name and its value. Within each list item, each attribute label has the CSS class "attribute-label", and each attribute value has the CSS class "attribute-value".

On the Cart page, View Order page, admin Edit Order page, and in the emails, the attributes are wrapped in a 'span' element with the CSS class "custom-attributes". Each attribute name and value pair is wrapped in a 'span' which has two CSS classes: its attribute name and its value. Within this span, each attribute label has the CSS class "attribute-label", and each attribute value has the CSS class "attribute-value".


= How do I remove the extra left-margin space from the attributes on the single product page? = 

Add this CSS:

`ul.custom-attributes {
  	margin-left: 0;
}`

= How do I make all the attribute labels bold? =

Add this CSS:

`.custom-attributes .attribute-label {
  font-weight: bold;
}`


= How do I make all the labels and values italic? = 

Add this CSS:

`.custom-attributes {
  font-style:italic
}`

== Screenshots ==

1. The attributes under the product name on the Edit Order page in the admin backend.
2. The attributes under the product name on the Cart page.
3. The attributes under the product name on the Customer's Order Details page on the front end.

== Changelog ==

= 1.3.1 =
* Fix - Fixed a fatal error that was breaking the Additional Information tab.
* Tweak - Translate attribute labels.

= 1.3 =
* Fix - Removed an error that caused installation to fail.
* New - Added a .pot localization file to make the plugin WPML ready.

= 1.2.5=
* Fix - Fixed a fatal error on the admin Edit Order page, above the item details. Please update.

= 1.2.4 =
* New - Show the attributes also on the Grouped product page, for each child product in the Grouped product.

= 1.2.3 =
* New - Option to show the attributes on the shop pages, which also includes the product category and tag archives.
* Fix - Typo in the description of one setting.
* Maintenenace: Updated plugin URI and added link to new Documentation.

= 1.2.2 =
* Fix - Attribute labels were temporarily hidden leaving only the attribute value showing in version 1.2.1.

= 1.2.1 =
* New - The "Visible on the product page" checkbox for each individual product will now be taken in to account on the single product page. If you uncheck that box, then the attribute will not be shown on the single product page. However, it will still show up on the rest of the places as described in the plugin description.
* New - Easily remove list bullets from the attributes on the single product page with the new option, "Show Attributes in a span Element". The new option can be found on the WooCommerce Settings Product tab, under "Product Data".
* New - Easily hide the labels and show only the values with the new option. The new option can be found on the WooCommerce Settings Product tab, under "Product Data".

* Fix - Check for WP_ERROR when using wp_get_post_terms.

= 1.2 =
* New - Custom attributes now also appear on the Cart page, on the View Order page on front end for customers, on the Edit Order admin page under Order Items, on the New Order email that goes to the administrator, on the Receipt (Order Processing) email that goes to the customer, and on the Order Complete email that goes to the customer.

= 1.1 =
* Fix - fixed logic for removing our custom product attributes from the Additional Information tab.

= 1.0 =
* Initial release.
== Upgrade Notice ==
= 1.2.5 =
Fixed a Fatal Error on the admin Edit Order page.

= 1.2.3 =
New option to show attributes on the shop pages, which also includes the product category and tag pages.

= 1.2.2 =
Fix - Attribute labels were temporarily hidden leaving only the attribute value showing in version 1.2.1.

= 1.2 =
New - Custom attributes now also appear on the Cart, View Order, admin Edit Order page, and in emails.
