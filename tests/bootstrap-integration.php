<?php
/**
 * Bootstrap for integration tests.
 *
 * Boots real WordPress from the core test library, then loads this plugin
 * before WP finishes initializing.
 *
 * WP_TESTS_DIR is set automatically inside wp-env. On the host, point it at a
 * WordPress develop checkout: export WP_TESTS_DIR=/path/to/wordpress-develop/tests/phpunit
 *
 * @package CartRules_OTIC
 */

declare( strict_types=1 );

$cartrules_otic_autoload = __DIR__ . '/../vendor/autoload.php';

if ( file_exists( $cartrules_otic_autoload ) ) {
	require_once $cartrules_otic_autoload;
}

$cartrules_otic_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $cartrules_otic_tests_dir ) {
	$cartrules_otic_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

$cartrules_otic_functions = $cartrules_otic_tests_dir . '/includes/functions.php';

if ( ! file_exists( $cartrules_otic_functions ) ) {
	fwrite(
		STDERR,
		"Could not find the WordPress test library at {$cartrules_otic_tests_dir}.\n" .
		"Start the local environment first:  npm run env:start\n" .
		"Or set WP_TESTS_DIR to a wordpress-develop/tests/phpunit checkout.\n"
	);
	exit( 1 );
}

require_once $cartrules_otic_functions;

/**
 * Load this plugin before WordPress finishes booting.
 *
 * REPLACE the filename below with your plugin's main file. For a theme, use
 * switch_theme() in a setup hook instead.
 *
 * @return void
 */
function cartrules_otic_manually_load_plugin() {
	require dirname( __DIR__ ) . '/cartrules-one-tag-in-cart-for-woocommerce.php';
}
tests_add_filter( 'muplugins_loaded', 'cartrules_otic_manually_load_plugin' );

require $cartrules_otic_tests_dir . '/includes/bootstrap.php';
