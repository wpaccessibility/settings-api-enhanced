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
function add_settings_fields_options_general() {
	sae_add_settings_section( 'site_identity', '', null, 'general' );

	sae_add_settings_field( 'blogname', __( 'Site title' ), 'text', 'general', 'site_identity', array(
		'input_class' => 'regular-text',
	) );

	sae_add_settings_field( 'blogdescription', __( 'Tagline' ), 'text', 'general', 'site_identity', array(
		'input_class'    => 'regular-text',
		'description_id' => 'tagline-description',
		'description'    => __( 'In a few words, explain what this site is about.' ),
	) );

	if ( ! is_multisite() ) {
		sae_add_settings_section( 'site_urls', '', null, 'general' );

		sae_add_settings_field( 'siteurl', __( 'WordPress address (URL)' ), 'url', 'general', 'site_urls', array(
			'input_class' => 'regular-text' . ( defined( 'WP_SITEURL' ) ? ' disabled' : '' ),
			'disabled'    => defined( 'WP_SITEURL' ) ? true : false,
		) );

		sae_add_settings_field( 'home', __( 'Site address (URL)' ), 'url', 'general', 'site_urls', array(
			'input_class' => 'regular-text' . ( defined( 'WP_HOME' ) ? ' disabled' : '' ),
			'disabled'    => defined( 'WP_HOME' ) ? true : false,
			'description' => __( 'Enter the address here if you <a href="https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory">want your site home page to be different from your WordPress installation directory.</a>' ),
		) );

		sae_add_settings_section( 'email', '', null, 'general' );

		sae_add_settings_field( 'admin_email', __( 'Email address' ), 'email', 'general', 'email', array(
			'input_id'    => 'admin-email',
			'input_class' => 'regular-text ltr',
			'description' => __( 'This address is used for admin purposes, like new user notification.' ),
		) );

		sae_add_settings_section( 'users', '', null, 'general' );

		sae_add_settings_field( 'users_can_register', __( 'Membership' ), 'checkbox', 'general', 'users', array(
			'label' => __( 'Anyone can register' ),
		) );

		sae_add_settings_field( 'default_role', __( 'New user default role' ), 'render_settings_field_roles_dropdown', 'general', 'users' );
	} else {
		sae_add_settings_section( 'email', '', null, 'general' );

		sae_add_settings_field( 'admin_email', __( 'Email address' ), 'email', 'general', 'email', array(
			'input_id'       => 'new_admin_email',
			'input_name'     => 'new_admin_email',
			'input_class'    => 'regular-text ltr',
			'description_id' => 'new-admin-email-description',
			'description'    => __( 'This address is used for admin purposes. If you change this we will send you an email at your new address to confirm it. <strong>The new address will not become active until confirmed.</strong>' ),
			'value_callback' => 'settings_field_admin_email_get_option',
			'after'          => 'settings_field_admin_email_after',
		) );
	}

	sae_add_settings_section( 'timezone', '', null, 'general' );

	sae_add_settings_field( 'timezone_string', __( 'Timezone' ), 'render_settings_field_timezones_dropdown', 'general', 'timezone', array(
		'description_id' => 'timezone-description',
		'description'    => __( 'Choose either a city in the same timezone as you or a UTC timezone offset.' ),
	) );

	sae_add_settings_section( 'locale', '', null, 'general' );

	sae_add_settings_field( 'date_format', __( 'Date format' ), 'render_settings_field_datetime_format_radio', 'general', 'locale', array(
		'mode'        => 'date_format',
		'fieldset'    => true,
		'input_class' => 'js-date-time-format',
	) );

	sae_add_settings_field( 'time_format', __( 'Time format' ), 'render_settings_field_datetime_format_radio', 'general', 'locale', array(
		'mode'        => 'time_format',
		'fieldset'    => true,
		'after'       => 'settings_field_time_format_after',
		'input_class' => 'js-date-time-format',
	) );

	/**
	 * @global WP_Locale $wp_locale
	 */
	global $wp_locale;

	$start_of_week_choices = array();
	for ( $day_index = 0; $day_index <= 6; $day_index++ ) {
		$start_of_week_choices[ $day_index ] = $wp_locale->get_weekday( $day_index );
	}

	sae_add_settings_field( 'start_of_week', __( 'The week starts on' ), 'select', 'general', 'locale', array(
		'choices'        => $start_of_week_choices,
	) );

	sae_add_settings_section( 'language', '', null, 'general' );

	$languages = get_available_languages();
	$translations = wp_get_available_translations();
	if ( ! is_multisite() && defined( 'WPLANG' ) && '' !== WPLANG && 'en_US' !== WPLANG && ! in_array( WPLANG, $languages ) ) {
		$languages[] = WPLANG;
	}
	if ( ! empty( $languages ) || ! empty( $translations ) ) {
		sae_add_settings_field( 'WPLANG', __( 'Site language' ), 'render_settings_field_languages_dropdown', 'general', 'language', array(
			'languages'    => $languages,
			'translations' => $translations,
			'after'        => 'settings_field_wplang_after',
		) );
	}
}

/**
 * Settings field callback to print a roles dropdown control.
 *
 * @since 4.8.0
 *
 * @see add_settings_field()
 *
 * @param array $field_args Array of field arguments.
 */
function render_settings_field_roles_dropdown( $field_args ) {
	$input_attrs = array(
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
			$input_attrs['aria-describedby'] = $field_args['description_id'];
		}
		$description_attrs['class'] = 'description';
	}

	$current = ! empty( $field_args['value'] ) ? $field_args['value'] : '';
	?>
	<select<?php attrs( $input_attrs ); ?>><?php wp_dropdown_roles( $current ); ?></select>
	<?php

	if ( ! empty( $field_args['description'] ) ) {
		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Settings field callback to print a languages dropdown control.
 *
 * @since 4.8.0
 *
 * @see add_settings_field()
 *
 * @param array $field_args Array of field arguments.
 */
function render_settings_field_languages_dropdown( $field_args ) {
	$languages    = isset( $field_args['languages'] ) ? $field_args['languages'] : get_available_languages();
	$translations = isset( $field_args['translations'] ) ? $field_args['translations'] : wp_get_available_translations();

	$locale = get_locale();
	if ( ! in_array( $locale, $languages ) ) {
		$locale = '';
	}

	wp_dropdown_languages( array(
		'name'         => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'id'           => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'selected'     => $locale,
		'languages'    => $languages,
		'translations' => $translations,
		'show_available_translations' => ( ! is_multisite() || is_super_admin() ) && wp_can_install_language_pack(),
	) );
}

/**
 * Settings field callback to print a timezones dropdown control.
 *
 * @since 4.8.0
 *
 * @see add_settings_field()
 *
 * @param array $field_args Array of field arguments.
 */
function render_settings_field_timezones_dropdown( $field_args ) {
	$current_offset = get_option('gmt_offset');
	$tzstring = ! empty( $field_args['value'] ) ? $field_args['value'] : '';

	$check_zone_info = true;

	// Remove old Etc mappings. Fallback to gmt_offset.
	if ( false !== strpos($tzstring,'Etc/GMT') )
		$tzstring = '';

	if ( empty($tzstring) ) { // Create a UTC+- zone if no timezone string exists
		$check_zone_info = false;
		if ( 0 == $current_offset )
			$tzstring = 'UTC+0';
		elseif ($current_offset < 0)
			$tzstring = 'UTC' . $current_offset;
		else
			$tzstring = 'UTC+' . $current_offset;
	}

	$input_attrs = array(
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
			$input_attrs['aria-describedby'] = $field_args['description_id'];
		}
		$description_attrs['class'] = 'description';
	}

	?>
	<select<?php attrs( $input_attrs ); ?>><?php echo wp_timezone_choice( $tzstring, get_user_locale() ); ?></select>
	<?php

	if ( ! empty( $field_args['description'] ) ) {
		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}

	$timezone_format = _x( 'Y-m-d H:i:s', 'timezone date format' );

	?>
	<p class="timezone-info">
		<span id="utc-time"><?php
			/* translators: 1: UTC abbreviation, 2: UTC time */
			printf( __( 'Universal time (%1$s) is %2$s.' ),
				'<abbr>' . __( 'UTC' ) . '</abbr>',
				date_i18n( $timezone_format, false, true )
			);
		?></span>
	<?php if ( get_option( 'timezone_string' ) || ! empty( $current_offset ) ) : ?>
		<span id="local-time"><?php
			/* translators: %s: local time */
			printf( __( 'Local time is %s.' ),
				date_i18n( $timezone_format )
			);
		?></span>
	<?php endif; ?>
	</p>

	<?php if ( $check_zone_info && $tzstring ) : ?>
	<p class="timezone-info">
	<span>
		<?php
		// Set TZ so localtime works.
		date_default_timezone_set($tzstring);
		$now = localtime(time(), true);
		if ( $now['tm_isdst'] )
			_e('This timezone is currently in daylight saving time.');
		else
			_e('This timezone is currently in standard time.');
		?>
		<br />
		<?php
		$allowed_zones = timezone_identifiers_list();

		if ( in_array( $tzstring, $allowed_zones) ) {
			$found = false;
			$date_time_zone_selected = new DateTimeZone($tzstring);
			$tz_offset = timezone_offset_get($date_time_zone_selected, date_create());
			$right_now = time();
			foreach ( timezone_transitions_get($date_time_zone_selected) as $tr) {
				if ( $tr['ts'] > $right_now ) {
				    $found = true;
					break;
				}
			}

			if ( $found ) {
				echo ' ';
				$message = $tr['isdst'] ?
					/* translators: %s: date and time  */
					__( 'Daylight saving time begins on: %s.')  :
					/* translators: %s: date and time  */
					__( 'Standard time begins on: %s.' );
				// Add the difference between the current offset and the new offset to ts to get the correct transition time from date_i18n().
				printf( $message,
					date_i18n(
						__( 'F j, Y' ) . ' ' . __( 'g:i a' ),
						$tr['ts'] + ( $tz_offset - $tr['offset'] )
					)
				);
			} else {
				_e( 'This timezone does not observe daylight saving time.' );
			}
		}
		// Set back to UTC.
		date_default_timezone_set('UTC');
		?>
		</span>
	</p>
	<?php endif;
}

/**
 * Settings field callback to print a date or time radio group.
 *
 * @since 4.8.0
 *
 * @see add_settings_field()
 *
 * @param array $field_args Array of field arguments.
 */
function render_settings_field_datetime_format_radio( $field_args ) {
	$input_attrs = array(
		'type'  => 'radio',
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	$choices = array();
	$custom_radio_label = $custom_radio_aria_label = $custom_label = '';

	if ( ! empty( $field_args['mode'] ) && 'time_format' === $field_args['mode'] ) {
		/**
		 * Filters the default time formats.
		 *
		 * @since 2.7.0
		 *
		 * @param array $default_time_formats Array of default time formats.
		 */
		$choices = array_unique( apply_filters( 'time_formats', array( __( 'g:i a' ), 'g:i A', 'H:i' ) ) );

		$custom_radio_label = __( 'Custom' );
		$custom_radio_aria_label = esc_attr( __( 'Custom: enter a custom time format in the following field' ) );
		$custom_label = __( 'Custom time format:' );
	} else {
		/**
		 * Filters the default date formats.
		 *
		 * @since 2.7.0
		 * @since 4.0.0 Added ISO date standard YYYY-MM-DD format.
		 *
		 * @param array $default_date_formats Array of default date formats.
		 */
		$choices = array_unique( apply_filters( 'date_formats', array( __( 'F j, Y' ), 'Y-m-d', 'm/d/Y', 'd/m/Y' ) ) );

		$custom_radio_label = _x( 'Custom', 'date or time format' );
		$custom_radio_aria_label = esc_attr( __( 'Custom: enter a custom date format in the following field' ) );
		$custom_label = __( 'Custom date format:' );
	}

	$current = ! empty( $field_args['value'] ) ? $field_args['value'] : $choices[0];

	$custom = false;
	if ( ! in_array( $current, $choices ) ) {
		$custom = true;
	}

	$id_suffix = 0;
	foreach ( $choices as $value ) {
		$id_suffix++;

		$radio_attrs = $input_attrs;
		$radio_attrs['id'] .= '-' . zeroise( $id_suffix, 2 );
		$radio_attrs['value'] = $value;

		echo '<span class="radio-item">';
		echo '<input' . attrs( $radio_attrs, false ) . checked( $current, $value, false ) . ' />';
		echo ' <label for="' . $radio_attrs['id'] . '" class="title-label"><span class="date-time-text format-i18n">' . date_i18n( $value ) . '</span><code>' . esc_html( $value ) . '</code></label>';
		echo '</span><br />';
	}

	$radio_attrs = $input_attrs;
	$radio_attrs['id'] = $radio_attrs['name'] . '_custom_radio';
	$radio_attrs['class'] = 'js-date-time-custom-format-radio';
	$radio_attrs['value'] = '\c\u\s\t\o\m';

	echo '<span class="radio-item">';
	echo '<input' . attrs( $radio_attrs, false ) . checked( $custom, true, false ) . ' />';
	echo ' <label for="' . $radio_attrs['id'] . '" class="title-label" aria-label="' . $custom_radio_aria_label . '">' . $custom_radio_label . '</label>';
	echo '</span>';

	$description_id = $radio_attrs['id'] . '-custom-description';
	$text_attrs = array(
		'type'             => 'text',
		'id'               => $radio_attrs['name'] . '_custom',
		'name'             => $radio_attrs['name'] . '_custom',
		'class'            => 'small-text date-time-custom-format-input js-date-time-custom-format-input',
		'value'            => $current,
		'aria-describedby' => $description_id,
	);

	echo '<span class="radio-item">';
	echo '<label for="' . esc_attr( $text_attrs['id'] ) . '" class="screen-reader-text">' . $custom_label . '</label>';
	echo ' <input' . attrs( $text_attrs, false ) . ' />';
	echo ' <span class="description" id="' . $description_id . '">' . __( 'Example:' ) . ' <span class="example">' . date_i18n( $current ) . '</span><span class="spinner js-date-time-custom-spinner"></span></span>';
	echo '</span>';
}

/**
 * Settings field callback to retrieve the admin email.
 *
 * @since 4.8
 *
 * @return string Admin email address.
 */
function settings_field_admin_email_get_option() {
	return get_option( 'admin_email' );
}

/**
 * Settings field callback to print additional content for the 'admin_email' control.
 *
 * @since 4.8
 */
function settings_field_admin_email_after() {
	$new_admin_email = get_option( 'new_admin_email' );
	if ( $new_admin_email && $new_admin_email != get_option('admin_email') ) : ?>
	<div class="updated inline">
	<p><?php
		printf(
			/* translators: %s: new admin email */
			__( 'There is a pending change of the admin email to %s.' ),
			'<code>' . esc_html( $new_admin_email ) . '</code>'
		);
		printf(
			' <a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( admin_url( 'options.php?dismiss=new_admin_email' ), 'dismiss-' . get_current_blog_id() . '-new_admin_email' ) ),
			__( 'Cancel' )
		);
	?></p>
	</div>
	<?php endif;
}

/**
 * Settings field callback to print additional content for the 'WPLANG' control.
 *
 * @since 4.8
 */
function settings_field_wplang_after() {
	// Add note about deprecated WPLANG constant.
	if ( defined( 'WPLANG' ) && ( '' !== WPLANG ) && get_locale() !== WPLANG ) {
		if ( is_multisite() && current_user_can( 'manage_network_options' )
			|| ! is_multisite() && current_user_can( 'manage_options' ) ) {
			?>
			<p class="description">
				<strong><?php _e( 'Note:' ); ?></strong> <?php printf( __( 'The %s constant in your %s file is no longer needed.' ), '<code>WPLANG</code>', '<code>wp-config.php</code>' ); ?>
			</p>
			<?php
		}
		_deprecated_argument( 'define()', '4.0.0', sprintf( __( 'The %s constant in your %s file is no longer needed.' ), 'WPLANG', 'wp-config.php' ) );
	}
}

/**
 * Settings field callback to print additional content for the 'time_format' control.
 *
 * @since 4.8
 */
function settings_field_time_format_after() {
	?>
	<p class="date-time-doc"><a href="https://codex.wordpress.org/Formatting_Date_and_Time"><?php _e( 'Documentation on date and time formatting' ); ?></a>.</p>
	<?php
}
