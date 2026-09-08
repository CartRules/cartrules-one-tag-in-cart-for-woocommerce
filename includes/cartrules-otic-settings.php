<?php

defined( 'ABSPATH' ) || exit;

/**
 * Adds this module's "One Tag in Cart" section to the shared "CartRules" tab.
 */

add_filter( 'woocommerce_get_sections_cartrules', 'cartrules_otic_add_settings_section' );
add_filter( 'woocommerce_get_settings_cartrules', 'cartrules_otic_settings_fields', 10, 2 );

function cartrules_otic_add_settings_section( array $sections ): array {
	$sections['otic'] = __( 'One Tag in Cart', 'cartrules-one-tag-in-cart-for-woocommerce' );

	return $sections;
}

function cartrules_otic_settings_fields( array $settings, string $section_id ): array {
	if ( 'otic' !== $section_id ) {
		return $settings;
	}

	return array(
		array(
			'title' => __( 'One Tag in Cart', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'type'  => 'title',
			/* translators: %s: link to the CartRules PRO plugin page. */
			'desc'  => sprintf(
				__( 'Prevent customers from mixing products with different tags in the same cart. Need more control? %1$s puts every cart rule (category, tag, brand, shipping class, product type) in one place. Each rule can target specific tags instead of "any one at a time" (for example, only keep "Gift Cards" separate), and you get unlimited custom rules with full control over when each one applies. It also checks for other cart restrictions already running on your store, whether from another CartRules plugin or your own custom code, so nothing quietly conflicts.', 'cartrules-one-tag-in-cart-for-woocommerce' ),
				'<a href="https://cartrules.com/product/cartrules-one-in-cart-pro/" target="_blank" rel="noopener noreferrer">CartRules One in Cart PRO</a>'
			),
			'id'    => 'cartrules_otic_settings_title',
		),
		array(
			'title'   => __( 'Enable restriction', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'desc'    => __( 'Only allow products with one tag in the cart at a time', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'id'      => 'cartrules_otic_enabled',
			'default' => 'no',
			'type'    => 'checkbox',
		),
		array(
			'title'   => __( 'When a different tag is added', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'desc'    => __( 'Choose what happens when a customer tries to add a product with a different tag', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'id'      => 'cartrules_otic_mode',
			'default' => 'deny',
			'type'    => 'select',
			'class'   => 'wc-enhanced-select',
			'options' => array(
				'deny'    => __( 'Block the new product and show an error', 'cartrules-one-tag-in-cart-for-woocommerce' ),
				'replace' => __( 'Empty the cart first, then add the new product', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			),
		),
		array(
			'title'    => __( 'Blocked message', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'desc_tip' => __( 'Shown when a product is blocked. Use {tag} for the tag already in the cart.', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'id'       => 'cartrules_otic_deny_message',
			'default'  => __( 'You already have products tagged "{tag}" in your cart. Please remove them first, or complete that order separately.', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'type'     => 'textarea',
			'css'      => 'width:100%; height: 75px;',
		),
		array(
			'title'    => __( 'Replaced message', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'desc_tip' => __( 'Shown when the cart is emptied and replaced. Use {tag} for the tag that was removed.', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'id'       => 'cartrules_otic_replace_message',
			'default'  => __( 'Your cart contained products tagged "{tag}", so we replaced them with your new selection.', 'cartrules-one-tag-in-cart-for-woocommerce' ),
			'type'     => 'textarea',
			'css'      => 'width:100%; height: 75px;',
		),
		array(
			'type' => 'sectionend',
			'id'   => 'cartrules_otic_settings_end',
		),
	);
}
