=== WooCommerce Show Attributes ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: custom product attributes, woocommerce product attributes, product attributes, custom attributes, woocommerce custom product attributes
Requires at least: 3.8
Tested up to: 3.9.2
Stable Tag: 1.0
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show WooCommerce custom product attributes on the product page above Add-To-Cart.

== Description ==

This is an extension for WooCommerce that will show your custom product attributes on the product page above the "Add to cart" button instead of in the "Additional Information" tab. This does NOT affect nor include attributes which are used for Variations.

This plugin does not remove the "Additional Information" tab altogether. Removing that tab is the wrong way to go because that tab is used for other things. That tab is used to display width and height dimensions for shipped items, as well as to display Variations. So I do not remove the tab. I only remove the custom attributes from the tab, since these custom attributes will now be displayed above the "Add to cart" button.

Fork it [on Github](https://github.com/isabelc/WooCommerce-Show-Attributes).

== Installation ==

1.  Download the plugin `.zip` file.

2.  Go to **Plugins -> Add New -> Upload** to upload the `.zip` file.

3.  Click "Activate" to activate the plugin.


== Frequently Asked Questions ==

= How can I style the attributes? = 

This plugin adds several CSS selectors so that you can style the output by adding your own CSS. The attributes are in an unordered list with the CSS id "woocommerce-show-attributes".  Each list item has two CSS classes: its attribute name and its value. Within each list item, each attribute label has the CSS class "attribute-label", and each attribute value has the CSS class "attribute-value".


= How do I remove the list bullets from the attributes? = 

Add this CSS:

`#woocommerce-show-attributes {
  list-style-type: none;
}`

= How do I remove the extra left-margin space from the attributes? = 

Add this CSS:

`#woocommerce-show-attributes {
  	margin-left: 0;
}`

= How do I make all the attribute labels bold? =

Add this CSS:

`#woocommerce-show-attributes .attribute-label {
  font-weight: bold;
}`


= How do I make all the labels and values italic? = 

Add this CSS:

`#woocommerce-show-attributes {
  font-style:italic
}`



== Changelog ==

= 1.0 =
* Initial release.
