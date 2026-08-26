<?php

defined( 'ABSPATH' ) || exit;

/**
 * Blocks or replaces cart contents when a product from a different tag is added.
 */
class CartRules_OTIC_Cart_Restriction {

	/**
	 * Cart item keys staged for removal in "replace" mode, keyed by the product id that
	 * triggered the replacement. Populated during validation, consumed by handle_replace().
	 *
	 * @var array<int, array{cart_item_keys: string[], tag_name: string}>
	 */
	private $pending_replacements = array();

	public function __construct() {
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_to_cart' ), 10, 2 );
		add_action( 'woocommerce_add_to_cart', array( $this, 'handle_replace' ), 10, 2 );
	}

	/**
	 * Other plugins may also hook woocommerce_add_to_cart_validation. Removing cart items here
	 * instead of in handle_replace() would mutate the cart mid-filter-chain, so a plugin
	 * validating later would see an already-emptied cart and skip its own check.
	 *
	 * @param bool $passed
	 * @param int  $product_id
	 */
	public function validate_add_to_cart( bool $passed, int $product_id ): bool {
		if ( ! $passed || 'yes' !== get_option( 'cartrules_otic_enabled', 'no' ) || WC()->cart->is_empty() ) {
			return $passed;
		}

		$new_tags = $this->get_product_tag_ids( $product_id );

		if ( empty( $new_tags ) ) {
			return $passed;
		}

		$cart_tags = array();

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$cart_tags = array_merge( $cart_tags, $this->get_product_tag_ids( $cart_item['product_id'] ) );
		}

		$cart_tags = array_values( array_unique( $cart_tags ) );

		if ( empty( $cart_tags ) || array_intersect( $new_tags, $cart_tags ) ) {
			return $passed;
		}

		$existing_tag_name = $this->get_tag_name( $cart_tags[0] );

		if ( 'replace' === get_option( 'cartrules_otic_mode', 'deny' ) ) {
			$this->pending_replacements[ $product_id ] = array(
				'cart_item_keys' => array_keys( WC()->cart->get_cart() ),
				'tag_name'       => $existing_tag_name,
			);

			return true;
		}

		wc_add_notice( $this->build_message( 'cartrules_otic_deny_message', $existing_tag_name ), 'error' );

		return false;
	}

	/**
	 * Runs once WooCommerce has actually added the new item to the cart, so every plugin
	 * hooked into the validation filter has already had a chance to evaluate the original cart.
	 *
	 * @param string $cart_item_key
	 * @param int    $product_id
	 */
	public function handle_replace( string $cart_item_key, int $product_id ): void {
		if ( ! isset( $this->pending_replacements[ $product_id ] ) ) {
			return;
		}

		$replacement = $this->pending_replacements[ $product_id ];
		unset( $this->pending_replacements[ $product_id ] );

		foreach ( $replacement['cart_item_keys'] as $conflicting_item_key ) {
			WC()->cart->remove_cart_item( $conflicting_item_key );
		}

		wc_add_notice( $this->build_message( 'cartrules_otic_replace_message', $replacement['tag_name'] ), 'notice' );
	}

	private function build_message( string $option_id, string $tag_name ): string {
		$message = get_option( $option_id );

		return str_replace( '{tag}', $tag_name, $message );
	}

	/**
	 * @return int[]
	 */
	private function get_product_tag_ids( int $product_id ): array {
		$terms = get_the_terms( $product_id, 'product_tag' );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return array();
		}

		return wp_list_pluck( $terms, 'term_id' );
	}

	private function get_tag_name( int $term_id ): string {
		$term = get_term( $term_id, 'product_tag' );

		return $term && ! is_wp_error( $term ) ? $term->name : '';
	}
}
