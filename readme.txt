=== NIF (Num. de Contribuinte Português) for WooCommerce ===
Contributors: webdados, wonderm00n
Tags: woocommerce, ecommerce, e-commerce, nif, nipc, vat, tax, portugal
Author URI: http://www.webdados.pt
Plugin URI: http://www.webdados.pt/produtos-e-servicos/internet/desenvolvimento-wordpress/nif-de-contribuinte-portugues-woocommerce-wordpress/
Requires at least: 4.4
Tested up to: 4.7.3
Stable tag: 3.0

This plugin adds the Portuguese NIF/NIPC as a new field to WooCommerce checkout and order details, if the billing address / customer is from Portugal.

== Description ==

This plugin adds the Portuguese VAT identification number (NIF/NIPC) as a new field to WooCommerce checkout and order details, if the billing address is from Portugal.

= Features: =

* Adds the Portuguese VAT identification number (NIF/NIPC) to the WooCommerce Checkout fields, Order admin fields, Order Emails and "Thank You" page;
* It's possible to edit the customer's NIF/NIPC field on My Account - Billing Address and on the User edit screen on wp-admin.
* NIF/NIPC check digit validation (if activated via filter)

== Installation ==

* Use the included automatic install feature on your WordPress admin panel and search for "NIF WooCommerce".

== Frequently Asked Questions ==

= How to make the NIF field required? =

Just add this to your theme's functions.php file (v3.0 and up):

`add_filter( 'woocommerce_nif_field_required', 'woocommerce_nif_field_required' );
function woocommerce_nif_field_required( $required ) {
	return true;
}`

= Is it possible to validate the check digit in order to ensure a valid Portuguese NIF/NIPC is entered by the customer? =

Yes, it is! Just add this to your theme's functions.php file (v3.0 and up):

`add_filter( 'woocommerce_nif_field_validate', 'woocommerce_nif_field_validate' );
function woocommerce_nif_field_validate( $validate ) {
	return true;
}`

== Changelog ==

= 3.0 =
* It's now possible to validate the Portuguese NIF/NIPC check digit entered by the customer (by returninig true on the `woocommerce_nif_field_validate` filter)
* Tested with WooCommerce 3.0.0-rc.2
* Changed version tests from 2.7 to 3.0
* New `autocomplete` parameter set to 'on'
* New `priority` parameter set to '120'
* New `maxlength` parameter set to '9'
* New filters to manipulate the field `label`, `placeholder`, `required`, `class`, `clear`, `autocomplete` and `maxlength` parameters (check filters_examples.php)
* Bumped `Tested up to` tag

= 2.1 =
* WooCommerce 2.7 compatibility
* NIF/NIPC is also shown and editable, in admin, on the user edit screen (alongside with other Billing Address fields)

= 2.0.2 =
* Fix typos on the readme.txt file (Thanks Daniel Matos)

= 2.0.1 =
* Bumped `Tested up to` and `Requires at least` tags

= 2.0 =
* Completely rewritten
* NIF/NIPC is added to the Billing Address fields on the Checkout (as long as the customer country is Portugal)
* You can also edit the user NIF/NIPC on the My Account - Billing Address form
* NIF/NIPC is also shown and editable on the order screen (alongside with other Billing Address fields)
* NIF/NIPC is added to the Customer Details section on Emails and Thank You page.

= 1.3 =
* Adds the field to the My Acccount / Edit Billing Address form

= 1.2.2 =
* The value is now auto filled with the last one used

= 1.2.1 =
* Small fix to avoid php notices

= 1.2 =
* WordPress Multisite support

= 1.1.1 =
* Forgot to update version number on the php file.

= 1.1 =
* Bug fix after WooCommerce 2.1 changes.

= 1.0 =
* Initial release.