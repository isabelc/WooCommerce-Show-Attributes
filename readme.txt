=== WooCommerce Show Attributes ===
Contributors: isabel104
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=me%40isabelcastillo%2ecom
Tags: custom product attributes, woocommerce product attributes, product attributes, custom attributes, woocommerce custom product attributes
Requires at least: 3.8
Tested up to: 4.0
Stable Tag: 1.2
License: GNU Version 2 or Any Later Version
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Show WooCommerce custom product attributes on the Product page, Cart page, admin Order Details page and emails.

== Description ==

This is an extension for WooCommerce that will show your custom product attributes on the single product page above the "Add to cart" button instead of in the "Additional Information" tab. This does NOT affect nor include attributes which are used for Variations.

The custom product product attributes will **also** be displayed on:

* Cart page
* View Order page on front end for customers
* Edit Order admin page, under Order Items
* New Order email that goes to the administrator
* Receipt (Order Processing) email that goes to the customer
* Order Complete email that goes to the customer

Fork it [on Github](https://github.com/isabelc/WooCommerce-Show-Attributes).

== Installation ==

1.  Download the plugin `.zip` file.

2.  Go to **Plugins -> Add New -> Upload** to upload the `.zip` file.

3.  Click "Activate" to activate the plugin.

4.  That is all. Now your product attributes will be displayed in all the places described in the plugin description.


== Frequently Asked Questions ==

= How can I style the attributes? = 

This plugin adds several CSS selectors so that you can style the output by adding your own CSS.

On the "single product page", the attributes are in an unordered list with the CSS class "custom-attributes". Each list item has two CSS classes: its attribute name and its value. Within each list item, each attribute label has the CSS class "attribute-label", and each attribute value has the CSS class "attribute-value".

On the Cart page, View Order page, admin Edit Order page, and in the emails, the attributes are wrapped in a 'span' element with the CSS class "custom-attributes". Each attribute name and value pair is wrapped in a 'span' which has two CSS classes: its attribute name and its value. Within this span, each attribute label has the CSS class "attribute-label", and each attribute value has the CSS class "attribute-value".


= How do I remove the list bullets from the attributes on the single product page? = 

Add this CSS:

`ul.custom-attributes {
  list-style-type: none;
}`

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

= 1.2 =
* New - Custom attributes now also appear on the Cart page, on the View Order page on front end for customers, on the Edit Order admin page under Order Items, on the New Order email that goes to the administrator, on the Receipt (Order Processing) email that goes to the customer, and on the Order Complete email that goes to the customer.

= 1.1 =
* Fix - fixed logic for removing our custom product attributes from the Additional Information tab.

= 1.0 =
* Initial release.
== Upgrade Notice ==

= 1.2 =
New - Custom attributes now also appear on the Cart, View Order, admin Edit Order page, and in emails.
