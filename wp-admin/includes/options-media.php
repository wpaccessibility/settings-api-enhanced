<?php
/**
 * WordPress Options implementation for General Settings.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.8.0
 */

/**
 * Adds default settings fields for the General Settings page.
 *
 * @since 4.8.0
 */
function add_settings_fields_options_media() {
	sae_add_settings_section( 'thumbnail-size', __( 'Image sizes' ), 'sizes_before', 'media' );

	sae_add_settings_field( 'thumbnail_size_w', __( 'Thumbnail width' ), 'number', 'media', 'thumbnail-size', array(
		'input_class' => 'small-text',
	) );

	sae_add_settings_field( 'thumbnail_size_h', __( 'Thumbnail height' ), 'number', 'media', 'thumbnail-size', array(
		'input_class' => 'small-text',
	) );

	sae_add_settings_field( 'thumbnail_crop', '', 'checkbox', 'media', 'thumbnail-size', array(
		'skip_title'  => true,
		'label'       => __( 'Crop thumbnails to exact dimensions' ),
		'description' => __( 'Normally thumbnails are proportional.' ),
	) );

	sae_add_settings_section( 'medium-size', '', null, 'media' );

	sae_add_settings_field( 'medium_size_w', __( 'Medium image max width' ), 'number', 'media', 'medium-size', array(
		'input_class' => 'small-text',
	) );

	sae_add_settings_field( 'medium_size_h', __( 'Medium image max height' ), 'number', 'media', 'medium-size', array(
		'input_class' => 'small-text',
	) );

	sae_add_settings_section( 'large-size', '', null, 'media' );

	sae_add_settings_field( 'large_size_w', __( 'Large image max width' ), 'number', 'media', 'large-size', array(
		'input_class' => 'small-text',
	) );

	sae_add_settings_field( 'large_size_h', __( 'Large image max height' ), 'number', 'media', 'large-size', array(
		'input_class' => 'small-text',
	) );

	// Is this 'embeds' section really used?
	if ( isset( $GLOBALS['wp_settings']['media']['embeds'] ) ) {
		sae_add_settings_section( 'embeds', '', null, 'media' );
		do_settings_fields( 'media', 'embeds' );
	}

	if ( ! is_multisite() ) {
		sae_add_settings_section( 'uploads', __( 'Uploading files' ), null, 'media' );

		// If upload_url_path is not the default (empty), and upload_path is not the default ('wp-content/uploads' or empty)
		if ( get_option( 'upload_url_path' ) || ( get_option( 'upload_path' ) !== 'wp-content/uploads' && get_option( 'upload_path' ) ) ) {
			sae_add_settings_field( 'upload_path', __( 'Store uploads in this folder' ), 'text', 'media', 'uploads', array(
				'input_class'    => 'regular-text',
				'description_id' => 'upload-path-description',
				'description'    => sprintf(
					/* translators: %s: wp-content/uploads */
					__( 'Default is %s' ), '<code>wp-content/uploads</code>'
				),
			) );

			sae_add_settings_field( 'upload_url_path', __( 'Full URL path to files' ), 'text', 'media', 'uploads', array(
				'input_class'    => 'regular-text',
				'description_id' => 'upload-path-description',
				'description'    => __( 'Configuring this is optional. By default, it should be blank.' ),
			) );
		}

		sae_add_settings_field( 'uploads_use_yearmonth_folders', '', 'checkbox', 'media', 'uploads', array(
			'skip_title'  => true,
			'label'       => __( 'Organize my uploads into month- and year-based folders' ),
		) );
	}

}

function sizes_before() {
	echo '<p>' . __( 'The sizes listed below determine the maximum dimensions in pixels to use when adding an image to the Media Library.' ) . '</p>';
}
