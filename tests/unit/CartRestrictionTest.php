<?php
/**
 * Unit tests for the cart-restriction engine — the actual "one tag at a time"
 * rule. No WordPress is loaded; WC()/get_option()/term functions are mocked
 * with WP_Mock.
 *
 * @package CartRules_OTIC
 */

declare( strict_types=1 );

namespace CartRules_OTIC\Tests\Unit;

use WP_Mock;
use WP_Mock\Tools\TestCase as WPMockTestCase;

require_once dirname( __DIR__, 2 ) . '/includes/class-cartrules-otic-cart-restriction.php';
require_once __DIR__ . '/FakeCart.php';

class CartRestrictionTest extends WPMockTestCase {

	private function mock_cart( array $items ): void {
		$wc       = new \stdClass();
		$wc->cart = new FakeCart( $items );

		WP_Mock::userFunction( 'WC' )->andReturn( $wc );
	}

	/**
	 * @param array<int, int[]> $product_to_term_ids Product id => term ids.
	 */
	private function mock_tags( array $product_to_term_ids ): void {
		WP_Mock::userFunction( 'get_the_terms' )->andReturnUsing(
			function ( $product_id ) use ( $product_to_term_ids ) {
				$term_ids = $product_to_term_ids[ $product_id ] ?? array();

				if ( empty( $term_ids ) ) {
					return false;
				}

				return array_map(
					static fn( $term_id ) => (object) array( 'term_id' => $term_id ),
					$term_ids
				);
			}
		);

		WP_Mock::userFunction( 'is_wp_error' )->andReturn( false );
		WP_Mock::userFunction( 'wp_list_pluck' )->andReturnUsing(
			static fn( $terms, $field ) => array_map( static fn( $term ) => $term->$field, $terms )
		);
	}

	public function test_denies_a_different_tag_in_deny_mode() {
		$restriction = new \CartRules_OTIC_Cart_Restriction();

		$this->mock_cart( array( 'key_1' => array( 'product_id' => 1 ) ) );
		$this->mock_tags(
			array(
				1 => array( 10 ),
				2 => array( 20 ),
			)
		);

		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_enabled', 'no' )->andReturn( 'yes' );
		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_mode', 'deny' )->andReturn( 'deny' );
		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_deny_message' )->andReturn( 'Blocked: {tag}' );
		WP_Mock::userFunction( 'get_term' )->with( 10, 'product_tag' )->andReturn( (object) array( 'name' => 'Fragile' ) );
		WP_Mock::userFunction( 'wc_add_notice' )->with( 'Blocked: Fragile', 'error' )->once();

		$this->assertFalse( $restriction->validate_add_to_cart( true, 2 ) );
	}

	public function test_replace_mode_empties_the_cart_instead_of_blocking() {
		$restriction = new \CartRules_OTIC_Cart_Restriction();

		$this->mock_cart( array( 'key_1' => array( 'product_id' => 1 ) ) );
		$this->mock_tags(
			array(
				1 => array( 10 ),
				2 => array( 20 ),
			)
		);

		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_enabled', 'no' )->andReturn( 'yes' );
		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_mode', 'deny' )->andReturn( 'replace' );
		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_replace_message' )->andReturn( 'Replaced: {tag}' );
		WP_Mock::userFunction( 'get_term' )->with( 10, 'product_tag' )->andReturn( (object) array( 'name' => 'Fragile' ) );
		WP_Mock::userFunction( 'wc_add_notice' )->with( 'Replaced: Fragile', 'notice' )->once();

		$this->assertTrue( $restriction->validate_add_to_cart( true, 2 ) );

		$restriction->handle_replace( 'new_key', 2 );
	}

	public function test_allows_a_shared_tag() {
		$restriction = new \CartRules_OTIC_Cart_Restriction();

		$this->mock_cart( array( 'key_1' => array( 'product_id' => 1 ) ) );
		$this->mock_tags(
			array(
				1 => array( 10 ),
				2 => array( 10 ),
			)
		);

		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_enabled', 'no' )->andReturn( 'yes' );

		$this->assertTrue( $restriction->validate_add_to_cart( true, 2 ) );
	}

	public function test_disabled_setting_lets_everything_through() {
		$restriction = new \CartRules_OTIC_Cart_Restriction();

		WP_Mock::userFunction( 'get_option' )->with( 'cartrules_otic_enabled', 'no' )->andReturn( 'no' );

		$this->assertTrue( $restriction->validate_add_to_cart( true, 2 ) );
	}
}
