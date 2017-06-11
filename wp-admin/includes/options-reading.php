<?php
/**
 * WordPress Options implementation for Reading Settings.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.8.0
 */

/**
 * Adds default settings fields for the Reading Settings page.
 *
 * @since 4.8.0
 */
function add_settings_fields_options_reading() {
	if ( get_pages() ) {
		if ( 'page' == get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) && ! get_option( 'page_for_posts' ) ) {
			update_option( 'show_on_front', 'posts' );
		}

		sae_add_settings_section( 'front_page', __( 'Front page' ), null, 'reading' );

		sae_add_settings_field( 'show_on_front', __( 'Front page displays' ), 'radio', 'reading', 'front_page', array(
			'class'   => 'front-static-pages js-front-static-pages',
			'choices' => array(
				'posts' => __( 'Your latest posts' ),
				'page'  => sprintf( __( 'A <a href="%s">static page</a> (select below)' ), 'edit.php?post_type=page' ),
			),
			'after'   => 'settings_field_show_on_front_after',
		) );
	} else {
		if ( 'posts' != get_option( 'show_on_front' ) ) {
			update_option( 'show_on_front', 'posts' );
		}

		sae_add_settings_section( 'front_page', '', 'settings_section_front_page_before', 'reading' );
	}

	sae_add_settings_section( 'blog', __( 'Blog' ), null, 'reading' );

	sae_add_settings_field( 'posts_per_page', __( 'Number of posts to show in blog pages' ), 'number', 'reading', 'blog', array(
		'step' => 1,
		'min'  => 1,
	) );

	sae_add_settings_field( 'posts_per_rss', __( 'Number of most recent items to show in syndication feeds' ), 'number', 'reading', 'blog', array(
		'step' => 1,
		'min'  => 1,
	) );

	sae_add_settings_field( 'rss_use_excerpt', __( 'Display mode for articles in a feed' ), 'radio', 'reading', 'blog', array(
		'choices' => array(
			'0' => __( 'Full text' ),
			'1' => __( 'Summary' ),
		),
	) );

	if ( has_action( 'blog_privacy_selector' ) ) {
		sae_add_settings_section( 'privacy', __( 'Privacy' ), null, 'reading' );

		sae_add_settings_field( 'blog_public', __( 'Front page displays' ), 'radio', 'reading', 'privacy', array(
			'choices'     => array(
				'1' => __( 'Allow search engines to index this site' ),
				'0' => __( 'Discourage search engines from indexing this site' ),
			),
			'description' => __( 'Note: Neither of these options blocks access to your site &mdash; it is up to search engines to honor your request.' ),
			'after'       => 'settings_field_blog_public_after',
		) );
	} else {
		sae_add_settings_section( 'privacy', __( 'Search Engine Visibility' ), null, 'reading' );

		sae_add_settings_field( 'blog_public', '', 'checkbox', 'reading', 'privacy', array(
			'label'       => __( 'Discourage search engines from indexing this site' ),
			'description' => __( 'It is up to search engines to honor this request.' ),
		) );
	}

	if ( ! in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) ) ) {
		sae_add_settings_section( 'charset', __( 'Charset' ), null, 'reading' );

		sae_add_settings_field( 'blog_charset', __( 'Encoding for pages and feeds' ), 'text', 'reading', 'charset', array(
			'input_class' => 'regular-text',
			'description' => __( 'The <a href="https://codex.wordpress.org/Glossary#Character_set">character encoding</a> of your site (UTF-8 is recommended)' ),
		) );
	}
}

/**
 * Settings section callback for the 'front_page' section.
 *
 * @since 4.8.0
 */
function settings_section_front_page_before() {
	if ( get_pages() ) {
		return;
	}

	?>
	<input name="show_on_front" type="hidden" value="posts" />
	<?php
}

/**
 * Settings field callback to print additional content for the 'show_on_front' control.
 *
 * @since 4.8.0
 */
function settings_field_show_on_front_after() {
	?>
	<ul>
		<li><label for="page_on_front"><?php printf( __( 'Front page: %s' ), wp_dropdown_pages( array( 'name' => 'page_on_front', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => get_option( 'page_on_front' ) ) ) ); ?></label></li>
		<li><label for="page_for_posts"><?php printf( __( 'Posts page: %s' ), wp_dropdown_pages( array( 'name' => 'page_for_posts', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'option_none_value' => '0', 'selected' => get_option( 'page_for_posts' ) ) ) ); ?></label></li>
	</ul>
	<?php if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) == get_option( 'page_on_front' ) ) : ?>
	<div id="front-page-warning" class="error inline"><p><?php _e( '<strong>Warning:</strong> these pages should not be the same!' ); ?></p></div>
	<?php endif; ?>
	<?php
}

/**
 * Settings field callback to print additional content for the 'blog_public' control.
 *
 * @since 4.8.0
 */
function settings_field_blog_public_after() {
	/**
	 * Enable the legacy 'Site Visibility' privacy options.
	 *
	 * By default the privacy options form displays a single checkbox to 'discourage' search
	 * engines from indexing the site. Hooking to this action serves a dual purpose:
	 * 1. Disable the single checkbox in favor of a multiple-choice list of radio buttons.
	 * 2. Open the door to adding additional radio button choices to the list.
	 *
	 * Hooking to this action also converts the 'Search Engine Visibility' heading to the more
	 * open-ended 'Site Visibility' heading.
	 *
	 * @since 2.1.0
	 */
	do_action( 'blog_privacy_selector' );
}
