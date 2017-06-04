<?php
/*
Plugin Name: Settings API Enhanced
Plugin URI:  https://github.com/wpaccessibility/settings-api-enhanced
Description: An improved WordPress Settings API with default render callbacks and a new accessible layout.
Version:     1.0.0-alpha
Author:      WordPress Core Contributors
Author URI:  https://make.wordpress.org/core/
License:     GNU General Public License v3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

defined( 'ABSPATH' ) || exit;

/**
 * Enqueues the stylesheet with the new forms.css styles.
 */
function sae_enqueue_forms_css() {
	wp_enqueue_style( 'sae-forms', plugin_dir_url( __FILE__ ) . 'wp-admin/css/forms.css', array( 'forms' ) );
}

/**
 * Replaces the Settings > General screen with the plugin variant.
 */
function sae_replace_options_general() {
	global $title, $parent_file, $submenu_file, $timezone_format;

	// Ensure submenu item is highlighted correctly.
	$submenu_file = 'options-general.php';

	require_once SAE_ABSPATH . 'wp-admin/options-general.php';

	exit;
}

/**
 * Replaces the Settings > Writing screen with the plugin variant.
 */
function sae_replace_options_writing() {
	global $title, $parent_file, $submenu_file;

	// Ensure submenu item is highlighted correctly.
	$submenu_file = 'options-writing.php';

	require_once SAE_ABSPATH . 'wp-admin/options-writing.php';

	exit;
}

/**
 * Replaces the Settings > Media screen with the plugin variant.
 */
function sae_replace_options_media() {
	global $title, $parent_file, $submenu_file;

	// Ensure submenu item is highlighted correctly.
	$submenu_file = 'options-media.php';

	require_once SAE_ABSPATH . 'wp-admin/options-media.php';

	exit;
}

/**
 * Loads the plugin files.
 */
function sae_load() {
	define( 'SAE_ABSPATH', plugin_dir_path( __FILE__ ) );

	if ( ! is_admin() ) {
		return;
	}

	$admin_path = SAE_ABSPATH . 'wp-admin/';

	require_once $admin_path . 'includes/template.php';
	require_once $admin_path . 'includes/options.php';

	add_action( 'admin_enqueue_scripts', 'sae_enqueue_forms_css' );
	add_action( 'load-options-general.php', 'sae_replace_options_general' );
	add_action( 'load-options-writing.php', 'sae_replace_options_writing' );
	add_action( 'load-options-media.php', 'sae_replace_options_media' );
}

sae_load();
