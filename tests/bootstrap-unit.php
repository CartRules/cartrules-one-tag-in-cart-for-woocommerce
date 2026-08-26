<?php
/**
 * Bootstrap for unit tests.
 *
 * No WordPress, no database. WP functions are mocked with WP_Mock, so these run
 * anywhere in milliseconds. Anything needing real WP behaviour (the database,
 * the query loop, core hooks actually firing) belongs in tests/integration/.
 *
 * @package CartRules_OTIC
 */

declare( strict_types=1 );

$cartrules_otic_autoload = __DIR__ . '/../vendor/autoload.php';

if ( ! file_exists( $cartrules_otic_autoload ) ) {
	fwrite( STDERR, "Run 'composer install' before running the tests.\n" );
	exit( 1 );
}

// WP_Mock relies on Patchwork to redefine functions, which must be loaded
// before the code under test is autoloaded.
require_once $cartrules_otic_autoload;

// Constants the plugin expects at include time. Add project-specific ones here.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'CARTRULES_OTIC_PLUGIN_DIR' ) ) {
	define( 'CARTRULES_OTIC_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
}

WP_Mock::bootstrap();
