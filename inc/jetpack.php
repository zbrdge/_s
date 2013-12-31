<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package P-Not-Comics Custom Theme
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function pnc_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'pnc_jetpack_setup' );
