=== WooCommerce Show Attributes ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: custom product attributes, woocommerce product attributes, product attributes, custom attributes, woocommerce custom product attributes
Requires at least: 3.8
Tested up to: 4.6
Stable tag: 1.5
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show WooCommerce custom product attributes on the Product, Shop, and Cart pages, admin Order Details page and emails.

== Description ==

This is an extension for WooCommerce that will show your custom product attributes on the single product page above the "Add to cart" button instead of in the "Additional Information" tab. This does NOT affect nor include attributes which are used for Variations.

The custom product product attributes will **also** be displayed at these locations (with option to turn them off):

* Grouped product page
* Shop page (including product category and tag archives) (Off by default. You must enable this option.)
* Cart page
* View Order page on front end for customers
* Emails that goes to the customer, including:
	* Receipt (Order Processing) email that goes to the customer
	* Order Complete email that goes to the customer
	* Customer Invoice email
* New Order email that goes to the administrator
* Admin Order Details page on the back end, under Order Items

Includes a .pot localization file to make this plugin WPML-ready.

See the [documentation](http://isabelcastillo.com/docs/category/woocommerce-show-attributes).

Fork it [on GitHub](https://github.com/isabelc/WooCommerce-Show-Attributes).

== Installation ==

1.  Download the plugin `.zip` file.

2.  Go to **Plugins -> Add New -> Upload** to upload the `.zip` file.

3.  Click "Activate" to activate the plugin.

4. For each attribute that you want to display, you must check the box for “Visible on the product page.” This is a WooCommerce native option and is found on the Edit Product page, under the individual attribute settings. If you uncheck that box, the attribute will not be shown.

5.  Optional settings are at WooCommerce Settings -> Products tab. Click on "WC Show Attributes" to see this plugin's options.


== Frequently Asked Questions ==

= How do I show only some attributes, while not showing others? =

For each attribute that you want to display, you must check the box for “Visible on the product page.” So, you can use that setting to show some attributes. Leave the box unchecked for the attributes that you do not want to show.

= Why are my custom attributes NOT showing up? =

For each attribute that you want to display, you must check the box for “Visible on the product page.” If you leave that box unchecked, that attribute will not be shown by this plugin.

= Can I show the product weight and/or dimensions above the Add to Cart button? =

Yes, since version 1.4.0. See this plugin's settings page to enable this.

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

= 1.5 =
* Fix - Fixed one possible incompatibility with plugins that manage custom Woocommerce Tabs.
* Fix - Blank tab no longer appears if a shop item has at one attribute that enables Used For Variations.
* Localization - Fixed an error that prevented several strings from being translated.
* Localization - Translate attribute values, as well. Previously, only attribute labels where translated, but not values, since values are user-created.
* Localization - Added Finnish language translation. Thanks to Arhi Paivarinta.
* Tweak - Display adjustments - Separated .attribute-label from the colon by wrapping the label's content in another span element. Removed the space before the colon. Removed obsolete white space. Thanks to amielucha.

= 1.4.2 =
* New - The attribute values in the emails are now vertically aligned.
* New - New option to show attribute terms as links. This only applies to Global Attributes. Global Attributes are created in Products -> Attributes.
* Fix - Output attributes as comma separated list instead of separate lines.
* Fix - Weight and Dimensions would not show up in locations enabled by the settings, unless the option the Show Attributes in that location was also enabled. 

= 1.4.1 =
* Fix - Avoid PHP errors when calling get_attributes.

= 1.4.0 =
* New - 16 new options are available to give you more granular control over where to show the attributes.
* New - The options have moved to their own page at WooCommerce Settings, Products tab, click on "WC Show Attributes" to see all the options.
* New - Options to show the product weight and dimensions in various places.
* Fix - Attributes were displayed on the Cart page even though the setting to make an attribute visible was unchecked.
* Maintenance - Updated .pot translation file.

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
= 1.4.1 =
Fix - Avoid PHP errors when calling get_attributes.

= 1.4.0 =
16 new options are available to give you more control over where to show the attributes.

= 1.2.5 =
Fixed a Fatal Error on the admin Edit Order page.

= 1.2.3 =
New option to show attributes on the shop pages, which also includes the product category and tag pages.

= 1.2.2 =
Fix - Attribute labels were temporarily hidden leaving only the attribute value showing in version 1.2.1.

= 1.2 =
New - Custom attributes now also appear on the Cart, View Order, admin Edit Order page, and in emails.
