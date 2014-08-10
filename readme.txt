=== WooCommerce Show Attributes ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: custom product attributes, woocommerce product attributes, product attributes, custom attributes, woocommerce custom product attributes
Requires at least: 3.8
Tested up to: 4.0
Stable Tag: 1.1
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show WooCommerce custom product attributes on the product page above Add-To-Cart.

== Description ==

This is an extension for WooCommerce that will show your custom product attributes on the product page above the "Add to cart" button instead of in the "Additional Information" tab. This does NOT affect nor include attributes which are used for Variations.

This plugin does not remove the "Additional Information" tab altogether, since that tab is used for other things. That tab is used to display dimensions and weight for shipped items, as well as to display Variations. So we do not remove the tab. We only remove the custom attributes from the tab, since these custom attributes will now be displayed above the "Add to cart" button.

Fork it [on Github](https://github.com/isabelc/WooCommerce-Show-Attributes).

== Installation ==

1.  Download the plugin `.zip` file.

2.  Go to **Plugins -> Add New -> Upload** to upload the `.zip` file.

3.  Click "Activate" to activate the plugin.

4.  That is all. Now your product attributes will be displayed above the "Add to Cart" button.


== Frequently Asked Questions ==

= How can I style the attributes? = 

This plugin adds several CSS selectors so that you can style the output by adding your own CSS. The attributes are in an unordered list with the CSS id "woocommerce-show-attributes".  Each list item has two CSS classes: its attribute name and its value. Within each list item, each attribute label has the CSS class "attribute-label", and each attribute value has the CSS class "attribute-value".


= How do I remove the list bullets from the attributes? = 

Add this CSS:

`.custom-attributes {
  list-style-type: none;
}`

= How do I remove the extra left-margin space from the attributes? = 

Add this CSS:

`.custom-attributes {
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



== Changelog ==

= 1.1 =
* Fix - fixed logic for removing our custom product attributes from the Additional Information tab.

= 1.0 =
* Initial release.