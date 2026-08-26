=== CartRules One Tag in Cart for WooCommerce ===
Contributors: businessbloomer
Tags: woocommerce, cart, product tag, restrict cart, checkout
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin ensures customers can only buy products with one tag at a time.

== Description ==

This plugin stops customers from mixing products with different tags in the same order. If a product is already in the cart, adding a product with a different tag will either be blocked, or the cart will be emptied first, depending on the option you choose.

This is useful for stores that need to keep certain product groups separate at checkout, for example pre-orders, personalized items, or products fulfilled by a different process.

Once activated, go to WooCommerce > Settings > CartRules > One Tag in Cart to turn the restriction on and choose what should happen.

Works with both the classic, shortcode-based cart and checkout, and the newer WooCommerce Cart and Checkout blocks.

== Frequently Asked Questions ==

= Which "tag" does this plugin use? =

WooCommerce's own built-in product tags (the native `product_tag` taxonomy).

= What happens if a product isn't tagged? =

It's not restricted. Only products that have at least one tag assigned are checked against what's already in the cart.

= What happens if a product has more than one tag? =

It's allowed, as long as it shares at least one tag with what's already in the cart.

= Does this work with variable products? =

Yes, it works with all product types.

= Does this work with the WooCommerce Cart and Checkout blocks, or only the classic shortcode-based cart? =

Both. The restriction is applied when a product is added to the cart, so it works the same way whether your store uses the classic cart/checkout pages or the block-based versions.

= Does this affect orders created or edited from wp-admin? =

No, the restriction only applies to the storefront cart. Orders added or changed from wp-admin are not affected.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`, or install it through the Plugins menu in WordPress directly.
2. Activate the plugin through the Plugins menu in WordPress.
3. Go to WooCommerce > Settings > CartRules > One Tag in Cart to turn the restriction on and choose what should happen.

== Changelog ==

= 1.0.0 =
* Initial release
