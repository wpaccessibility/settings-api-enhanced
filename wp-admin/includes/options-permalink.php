<?php
/**
 * WordPress Options implementation for Permalink Settings.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.8.0
 */

/**
 * Adds default settings fields for the Permalink Settings page.
 *
 * @since 4.8.0
 */
function add_settings_fields_options_permalink() {
	sae_add_settings_section( 'common', __( 'Common Settings' ), null, 'permalink' );

	sae_add_settings_field( 'permalink_structure', __( 'Structure' ), 'render_settings_field_permalink_structure', 'permalink', 'common', array(
		'class'          => 'permalink-structure',
		'fieldset'       => true,
		'value_callback' => 'get_settings_field_option_permalink',
	) );

	sae_add_settings_section( 'optional', __( 'Optional' ), 'settings_section_optional_before', 'permalink' );

	sae_add_settings_field( 'category_base', __( 'Category base' ), 'text', 'permalink', 'optional', array(
		'input_class'    => 'regular-text code',
		'value_callback' => 'get_settings_field_option_permalink',
	) );

	sae_add_settings_field( 'tag_base', __( 'Tag base' ), 'text', 'permalink', 'optional', array(
		'input_class'    => 'regular-text code',
		'value_callback' => 'get_settings_field_option_permalink',
	) );

}

/**
 * Settings field callback to print the permalink structure field.
 *
 * @since 4.8.0
 *
 * @see add_settings_field()
 *
 * @param array $field_args Array of field arguments.
 */
function render_settings_field_permalink_structure( $field_args ) {
	$prefix = $blog_prefix = '';
	if ( ! got_url_rewrite() ) {
		$prefix = '/index.php';
	}

	$permalink_structure = get_option( 'permalink_structure' );
	if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( $permalink_structure, '/blog/' ) ) {
		$blog_prefix = '/blog';
	}

	$choices = array(
		array(
			'value'   => '',
			'label'   => __( 'Plain' ),
			'preview' => get_option( 'home' ) . '/?p=123',
		),
		array(
			'value'   => $prefix . '/%year%/%monthnum%/%day%/%postname%/',
			'label'   => __( 'Day and name' ),
			'preview' => get_option( 'home' ) . $blog_prefix . $prefix . '/' . date( 'Y' ) . '/' . date( 'm' ) . '/' . date( 'd' ) . '/' . _x( 'sample-post', 'sample permalink structure' ) . '/',
		),
		array(
			'value'   => $prefix . '/%year%/%monthnum%/%postname%/',
			'label'   => __( 'Month and name' ),
			'preview' => get_option( 'home' ) . $blog_prefix . $prefix . '/' . date('Y') . '/' . date('m') . '/' . _x( 'sample-post', 'sample permalink structure' ) . '/',
		),
		array(
			'value'   => $prefix . '/' . _x( 'archives', 'sample permalink base' ) . '/%post_id%',
			'label'   => __( 'Numeric' ),
			'preview' => get_option( 'home' ) . $blog_prefix . $prefix . '/' . _x( 'archives', 'sample permalink base' ) . '/123',
		),
		array(
			'value'   => $prefix . '/%postname%/',
			'label'   => __( 'Post name' ),
			'preview' => get_option( 'home' ) . $blog_prefix . $prefix . '/' . _x( 'sample-post', 'sample permalink structure' ) . '/',
		),
	);

	$current = ! empty( $field_args['value'] ) ? $field_args['value'] : $choices[0]['value'];

	$input_attrs = array(
		'type'  => 'radio',
		'id'    => 'selection',
		'name'  => 'selection',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	$id_suffix = 0;
	$custom = true;
	foreach ( $choices as $choice ) {
		$id_suffix++;

		$radio_attrs = $input_attrs;
		$radio_attrs['id'] .= '-' . zeroise( $id_suffix, 2 );
		$radio_attrs['value'] = $choice['value'];

		if ( $current === $choice['value'] ) {
			$custom = false;
		}

		echo '<span class="radio-item permalink-structure-radio-item">';
		echo '<input' . attrs( $radio_attrs, false ) . checked( $current, $choice['value'], false ) . ' />';
		echo ' <label for="' . $radio_attrs['id'] . '">' . $choice['label'] . '</label><code>' . esc_html( $choice['preview'] ) . '</code>';
		echo '</span><br />';
	}

	$radio_attrs = $input_attrs;
	$radio_attrs['id'] = 'custom_selection';
	$radio_attrs['value'] = 'custom';

	$text_attrs = array(
		'type'             => 'text',
		'id'               => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'             => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class'            => 'regular-text code',
		'value'            => $current,
	);

	echo '<span class="radio-item permalink-structure-radio-item">';
	echo '<input' . attrs( $radio_attrs, false ) . checked( $custom, true, false ) . ' />';
	echo ' <label for="' . $radio_attrs['id'] . '">' . __( 'Custom Structure' ) . '</label><code>' . esc_html( get_option( 'home' ) . $blog_prefix ) . '</code> <input' . attrs( $text_attrs, false ) . ' />';
	echo '</span><br />';
}

/**
 * Settings section callback for the 'optional' section.
 *
 * @since 4.8.0
 */
function settings_section_optional_before() {
	$prefix = $blog_prefix = '';
	if ( ! got_url_rewrite() ) {
		$prefix = '/index.php';
	}

	$permalink_structure = get_option( 'permalink_structure' );
	if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( $permalink_structure, '/blog/' ) ) {
		$blog_prefix = '/blog';
	}

	/* translators: %s is a placeholder that must come at the start of the URL. */
	echo '<p>' . sprintf( __( 'If you like, you may enter custom structures for your category and tag URLs here. For example, using <code>topics</code> as your category base would make your category links like <code>%s/topics/uncategorized/</code>. If you leave these blank the defaults will be used.' ), get_option( 'home' ) . $blog_prefix . $prefix ) . '</p>';
}

/**
 * Retrieves the value for one of the settings fields 'permalink_structure', 'category_base' or 'tag_base'.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 * @return mixed The value for the settings field, or null if no value set.
 */
function get_settings_field_option_permalink( $field_args ) {
	if ( empty( $field_args['input_name'] ) ) {
		return null;
	}

	$permalink_structure = get_option( 'permalink_structure' );
	$value = get_option( $field_args['input_name'] );

	if ( is_multisite() && ! is_subdomain_install() && is_main_site() && 0 === strpos( $permalink_structure, '/blog/' ) ) {
		$value = preg_replace( '|^/?blog|', '', $value );
	}

	return $value;
}
