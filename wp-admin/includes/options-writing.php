<?php
/**
 * WordPress Options implementation for Writing Settings.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.8.0
 */

/**
 * Adds default settings fields for the Writing Settings page.
 *
 * @since 4.8.0
 */
function add_settings_fields_options_writing() {
	$defaults_section_title = '';

	if ( get_site_option( 'initial_db_version' ) < 32453 ) {
		$defaults_section_title = __( 'Defaults' );

		sae_add_settings_section( 'formatting', __( 'Formatting' ), null, 'writing' );

		sae_add_settings_field( 'use_smilies', '', 'checkbox', 'writing', 'formatting', array(
			'skip_title'  => true,
			'label'       => __( 'Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics on display' ),
		) );

		sae_add_settings_field( 'use_balanceTags', '', 'checkbox', 'writing', 'formatting', array(
			'skip_title'  => true,
			'label'       => __( 'WordPress should correct invalidly nested XHTML automatically' ),
		) );
	}

	sae_add_settings_section( 'defaults', $defaults_section_title, null, 'writing' );

	sae_add_settings_field( 'default_category', __( 'Default Post Category' ), 'render_settings_field_categories_dropdown', 'writing', 'defaults', array(
		'taxonomy'     => 'category',
		'hierarchical' => true,
		'hide_empty'   => false,
	) );

	$post_format_choices = get_post_format_strings();
	unset( $post_format_choices['standard'] );
	array_unshift( $post_format_choices, get_post_format_string( 'standard' ) );

	sae_add_settings_field( 'default_post_format', __( 'Default Post Format' ), 'select', 'writing', 'defaults', array(
		'choices' => $post_format_choices,
	) );

	if ( get_option( 'link_manager_enabled' ) ) {
		sae_add_settings_field( 'default_link_category', __( 'Default Link Category' ), 'render_settings_field_categories_dropdown', 'writing', 'defaults', array(
			'taxonomy'     => 'link_category',
			'hierarchical' => true,
			'hide_empty'   => false,
		) );
	}

	/** This filter is documented in wp-admin/options.php */
	if ( apply_filters( 'enable_post_by_email_configuration', true ) ) {
		sae_add_settings_section( 'post_via_email', __( 'Post via email' ), 'settings_section_post_via_email_before', 'writing' );

		sae_add_settings_field( 'mailserver_url', __( 'Mail Server' ), 'text', 'writing', 'post_via_email', array(
			'input_class' => 'regular-text code',
		) );

		sae_add_settings_field( 'mailserver_port', __( 'Port' ), 'text', 'writing', 'post_via_email', array(
			'input_class' => 'small-text',
		) );

		sae_add_settings_field( 'mailserver_login', __( 'Login Name' ), 'text', 'writing', 'post_via_email', array(
			'input_class' => 'regular-text ltr',
		) );

		sae_add_settings_field( 'mailserver_pass', __( 'Password' ), 'text', 'writing', 'post_via_email', array(
			'input_class' => 'regular-text ltr',
		) );

		sae_add_settings_field( 'default_email_category', __( 'Default Mail Category' ), 'render_settings_field_categories_dropdown', 'writing', 'post_via_email', array(
			'taxonomy'     => 'category',
			'hierarchical' => true,
			'hide_empty'   => false,
		) );
	}

	/**
	 * Filters whether to enable the Update Services section in the Writing settings screen.
	 *
	 * @since 3.0.0
	 *
	 * @param bool $enable Whether to enable the Update Services settings area. Default true.
	 */
	if ( apply_filters( 'enable_update_services_configuration', true ) ) {
		sae_add_settings_section( 'update_services', __( 'Update Services' ), 'settings_section_update_services_before', 'writing' );

		if ( 1 == get_option( 'blog_public' ) ) {
			$ping_sites_label = __( 'When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see <a href="https://codex.wordpress.org/Update_Services">Update Services</a> on the Codex. Separate multiple service URLs with line breaks.' );

			sae_add_settings_field( 'ping_sites', $ping_sites_label, 'textarea', 'writing', 'update_services', array(
				'input_class' => 'large-text code',
				'rows'        => 3,
			) );
		}
	}
}

/**
 * Settings field callback to print a categories dropdown control.
 *
 * @since 4.8.0
 *
 * @see add_settings_field()
 *
 * @param array $field_args Array of field arguments.
 */
function render_settings_field_categories_dropdown( $field_args ) {
	wp_dropdown_categories( array(
		'name'         => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'id'           => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'class'        => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
		'selected'     => ! empty( $field_args['value'] ) ? $field_args['value'] : '',
		'orderby'      => 'name',
		'taxonomy'     => ! empty( $field_args['taxonomy'] ) ? $field_args['taxonomy'] : 'category',
		'hierarchical' => ! empty( $field_args['hierarchical'] ) ? $field_args['hierarchical'] : false,
		'hide_empty'   => ! empty( $field_args['hide_empty'] ) ? $field_args['hide_empty'] : true,
	) );
}

/**
 * Settings section callback for the 'post_via_email' section.
 *
 * @since 4.8.0
 */
function settings_section_post_via_email_before() {
	?>
	<p><?php
	printf(
		/* translators: 1, 2, 3: examples of random email addresses */
		__( 'To post to WordPress by email you must set up a secret email account with POP3 access. Any mail received at this address will be posted, so it&#8217;s a good idea to keep this address very secret. Here are three random strings you could use: %1$s, %2$s, %3$s.' ),
		sprintf( '<kbd>%s</kbd>', wp_generate_password( 8, false ) ),
		sprintf( '<kbd>%s</kbd>', wp_generate_password( 8, false ) ),
		sprintf( '<kbd>%s</kbd>', wp_generate_password( 8, false ) )
	);
	?></p>
	<?php
}

/**
 * Settings section callback for the 'update_services' section.
 *
 * @since 4.8.0
 */
function settings_section_update_services_before() {
	if ( 1 == get_option( 'blog_public' ) ) {
		return;
	}

	?>
	<p><?php printf( __( 'WordPress is not notifying any <a href="https://codex.wordpress.org/Update_Services">Update Services</a> because of your site&#8217;s <a href="%s">visibility settings</a>.' ), 'options-reading.php' ); ?></p>
	<?php
}
