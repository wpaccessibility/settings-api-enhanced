<?php
/**
 * Template WordPress Administration API.
 *
 * Enhanced prefixed variants of existing Core functions as well as some new functions.
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Add a new section to a settings page.
 *
 * This only calls the original function since it does not need to be adjusted.
 * The prefixed function is included here for completeness.
 *
 * See {@see add_settings_section()} for supported parameters.
 */
function sae_add_settings_section($id, $title, $callback, $page) {
	add_settings_section( $id, $title, $callback, $page );
}

/**
 * Add a new field to a section of a settings page
 *
 * Part of the Settings API. Use this to define a settings field that will show
 * as part of a settings section inside a settings page. The fields are shown using
 * do_settings_fields() in do_settings-sections()
 *
 * The $callback argument should be the name of a function that echoes out the
 * html input tags for this setting field. Use get_option() to retrieve existing
 * values to show.
 *
 * @since 2.7.0
 * @since 4.2.0 The `$class` argument was added.
 *
 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
 *
 * @param string          $id       Slug-name to identify the field. Used in the 'id' attribute of tags.
 * @param string          $title    Formatted title of the field. Shown as the label for the field
 *                                  during output.
 * @param callable|string $callback Function that fills the field with the desired form inputs. The
 *                                  function should echo its output. May instead be one out of 'text',
 *                                  'email', 'url', 'tel', 'number', 'textarea', 'checkbox', 'select',
 *                                  'radio' or 'multibox' to use a default function to render the form
 *                                  input.
 * @param string          $page     The slug-name of the settings page on which to show the section
 *                                  (general, reading, writing, ...).
 * @param string          $section  Optional. The slug-name of the section of the settings page
 *                                  in which to show the box. Default 'default'.
 * @param array           $args {
 *     Optional. Extra arguments used when outputting the field. There are several arguments that only apply
 *     for specific $callback values.
 *
 *     @type string          $input_id                 The 'id' attribute of the input field. Default is the
 *                                                     value of $id.
 *     @type string          $input_name               The `name` attribute of the input field. Default is the
 *                                                     value of $id.
 *     @type string          $input_class              CSS class to be added to the input field element when
 *                                                     it is output. Default empty.
 *     @type string          $label_for                When supplied, the setting title will be wrapped
 *                                                     in a `<label>` element, its `for` attribute populated
 *                                                     with this value. Default is the value of $input_id.
 *     @type string          $label_class              CSS class to be added to the label field element when
 *                                                     it is output. Default empty.
 *     @type string          $class                    CSS class to be added to the `<tr>` element when the
 *                                                     field is output. Default empty.
 *     @type string          $description              When supplied, this description will be shown below the
 *                                                     input field when using a default callback function.
 *     @type string          $description_id           When supplied, this value will be used for the `id` attribute
 *                                                     of the description element. Default is the value of $input_id
 *                                                     suffixed with '-description'.
 *     @type bool            $fieldset                 Whether to wrap the control in a fieldset and use the title
 *                                                     as its `legend`. Default true if $callback is either 'radio'
 *                                                     or 'multibox', otherwise false.
 *     @type callable        $value_callback           Callback to retrieve the value. Default is
 *                                                     'get_settings_field_option', which calls get_option()
 *                                                     based on the $input_name argument.
 *     @type bool            $skip_title               Whether to not print any field title. This can be useful for
 *                                                     single checkboxes that have their label printed manually.
 *                                                     Default false.
 *     @type bool            $skip_title_screen_reader Whether to hide the field title for screen readers. This can
 *                                                     be useful if a title should be present for visual purposes,
 *                                                     but does not convey a meaningful message. Default true if
 *                                                     $callback is 'checkbox', otherwise false.
 *     @type string|callable $before                   Can be supplied to generate additional output before the
 *                                                     field control. It can be either a string or a callback
 *                                                     to generate output. Default null.
 *     @type string|callable $after                    Can be supplied to generate additional output after the
 *                                                     field control. It can be either a string or a callback
 *                                                     to generate output. Default null.
 *     @type int|float       $min                      Can be used to set a minimum allowed value if $callback is
 *                                                     'number'. Default null.
 *     @type int|float       $max                      Can be used to set a maximum allowed value if $callback is
 *                                                     'number'. Default null.
 *     @type int|float       $step                     Can be used to set the numeric step for a field if $callback
 *                                                     is 'number'. Default null.
 *     @type int             $rows                     Can be used to set the number of rows if $callback is 'textarea'.
 *                                                     Default null.
 *     @type int             $cols                     Can be used to set the number of columns if $callback is
 *                                                     'textarea'. Default null.
 *     @type array           $choices                  Array of `$value => $label` pairs to be used as choices if
 *                                                     $callback is either 'select', 'radio' or 'multibox'. Default
 *                                                     empty array.
 *     @type bool            $multiple                 Whether the user should be able to select multiple values if
 *                                                     $callback is 'select'. Default false.
 * }
 */
function sae_add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()) {
	global $wp_settings_fields;

	if ( 'misc' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.0.0',
			/* translators: %s: misc */
			sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ),
				'misc'
			)
		);
		$page = 'general';
	}

	if ( 'privacy' == $page ) {
		_deprecated_argument( __FUNCTION__, '3.5.0',
			/* translators: %s: privacy */
			sprintf( __( 'The "%s" options group has been removed. Use another settings group.' ),
				'privacy'
			)
		);
		$page = 'reading';
	}

	$defaults = array(
		'input_id'                 => $id,
		'input_name'               => $id,
		'input_class'              => 'settings-field-control',
		'label_class'              => '',
		'class'                    => '',
		'description'              => '',
		'fieldset'                 => false,
		'value_callback'           => 'get_settings_field_option',
		'skip_title'               => false,
		'skip_title_screen_reader' => false,
		'before'                   => null,
		'after'                    => null,
	);

	switch ( $callback ) {
		case 'text':
		case 'email':
		case 'url':
		case 'tel':
			$defaults['type'] = $callback;
			$callback = 'render_settings_field_text';
			break;
		case 'number':
			$defaults['min'] = null;
			$defaults['max'] = null;
			$defaults['step'] = null;
			$callback = 'render_settings_field_number';
			break;
		case 'textarea':
			$defaults['rows'] = null;
			$defaults['cols'] = null;
			$callback = 'render_settings_field_textarea';
			break;
		case 'checkbox':
			$defaults['label'] = $title;
			$defaults['skip_title_screen_reader'] = true;
			$callback = 'render_settings_field_checkbox';
			break;
		case 'select':
			$defaults['choices'] = array();
			$defaults['multiple'] = false;
			$callback = 'render_settings_field_select';
			break;
		case 'radio':
		case 'multibox':
			$defaults['choices'] = array();
			$defaults['fieldset'] = true;
			$callback = 'render_settings_field_' . $callback;
			break;
	}

	$args = wp_parse_args( $args, $defaults );

	if ( ! empty( $args['input_id'] ) ) {
		if ( ! isset( $args['label_for'] ) ) {
			$args['label_for'] = $args['input_id'];
		}
		if ( ! isset( $args['description_id'] ) ) {
			$args['description_id'] = $args['input_id'] . '-description';
		}
	}

	$input_classes = explode( ' ', $args['input_class'] );
	if ( ! in_array( 'settings-field-control', $input_classes, true ) ) {
		$input_classes[] = 'settings-field-control';
		$args['input_class'] = implode( ' ', $input_classes );
	}

	$wp_settings_fields[$page][$section][$id] = array('id' => $id, 'title' => $title, 'callback' => $callback, 'args' => $args);
}

/**
 * Prints out all settings sections added to a particular settings page
 *
 * Part of the Settings API. Use this in a settings page callback function
 * to output all the sections and fields that were added to that $page with
 * add_settings_section() and add_settings_field()
 *
 * @global $wp_settings_sections Storage array of all settings sections added to admin pages
 * @global $wp_settings_fields Storage array of settings fields and info about their pages/sections
 * @since 2.7.0
 *
 * @param string $page The slug name of the page whose settings sections you want to output
 */
function sae_do_settings_sections( $page ) {
	global $wp_settings_sections, $wp_settings_fields;

	if ( ! isset( $wp_settings_sections[$page] ) )
		return;

	foreach ( (array) $wp_settings_sections[$page] as $section ) {
		echo '<div class="settings-section">';

		if ( $section['title'] )
			echo "<h2>{$section['title']}</h2>\n";

		if ( $section['callback'] )
			call_user_func( $section['callback'], $section );

		if ( isset( $wp_settings_fields ) && isset( $wp_settings_fields[ $page ] ) && isset( $wp_settings_fields[ $page ][ $section['id'] ] ) ) {
			echo '<div class="settings-fields">';
			sae_do_settings_fields( $page, $section['id'] );
			echo '</div>';
		}

		echo '</div>';
	}
}

/**
 * Print out the settings fields for a particular settings section
 *
 * Part of the Settings API. Use this in a settings page to output
 * a specific section. Should normally be called by do_settings_sections()
 * rather than directly.
 *
 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
 *
 * @since 2.7.0
 *
 * @param string $page Slug title of the admin page who's settings fields you want to show.
 * @param string $section Slug title of the settings section who's fields you want to show.
 */
function sae_do_settings_fields($page, $section) {
	global $wp_settings_fields;

	if ( ! isset( $wp_settings_fields[$page][$section] ) )
		return;

	foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
		$class = 'settings-field';

		if ( ! empty( $field['args']['class'] ) ) {
			$class .= ' ' . $field['args']['class'];
		}

		$wrap = ! empty( $field['args']['fieldset'] ) ? 'fieldset' : 'div';

		echo '<' . $wrap . ' class="' . esc_attr( $class ) . '">';

		if ( empty( $field['args']['skip_title'] ) ) {
			$label_class = 'settings-field-title';

			if ( ! empty( $field['args']['label_class'] ) ) {
				$label_class .= ' ' . $field['args']['label_class'];
			}

			if ( ! empty( $field['args']['fieldset'] ) ) {
				echo '<legend class="' . esc_attr( $label_class ) . '">' . $field['title'] . '</legend>';
			} elseif ( ! empty( $field['args']['skip_title_screen_reader'] ) ) {
				echo '<span class="' . esc_attr( $label_class ) . '" aria-hidden="true">' . $field['title'] . '</span>';
			} elseif ( ! empty( $field['args']['label_for'] ) ) {
				echo '<label for="' . esc_attr( $field['args']['label_for'] ) . '" class="' . esc_attr( $label_class ) . '">' . $field['title'] . '</label>';
			} else {
				echo '<span class="' . esc_attr( $label_class ) . '">' . $field['title'] . '</span>';
			}
		}

		// Duplicate arguments to not modify globals permanently.
		$field_args = $field['args'];

		if ( ! empty( $field_args['value_callback'] ) ) {
			$field_args['value'] = call_user_func( $field_args['value_callback'], $field_args );
		}

		if ( $field_args['before'] ) {
			if ( is_callable( $field_args['before'] ) ) {
				call_user_func( $field_args['before'], $field_args );
			} elseif ( is_string( $field_args['before'] ) ) {
				echo $field_args['before'];
			}
		}

		call_user_func( $field['callback'], $field_args );

		if ( $field_args['after'] ) {
			if ( is_callable( $field_args['after'] ) ) {
				call_user_func( $field_args['after'], $field_args );
			} elseif ( is_string( $field_args['after'] ) ) {
				echo $field_args['after'];
			}
		}

		echo '</' . $wrap . '>';
	}
}

/**
 * Renders a text input for a settings field.
 *
 * This function is used as a default callback when specifying 'text',
 * 'email', 'url' or 'tel' for the $callback parameter in
 * `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 */
function render_settings_field_text( $field_args ) {
	$input_attrs = array(
		'type'  => ! empty( $field_args['type'] ) ? $field_args['type'] : 'text',
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
		'value' => ! empty( $field_args['value'] ) ? $field_args['value'] : '',
	);

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
			$input_attrs['aria-describedby'] = $field_args['description_id'];
		}
		$description_attrs['class'] = 'description';
	}

	echo '<input' . attrs( $input_attrs, false ) . ' />';

	if ( ! empty( $field_args['description'] ) ) {
		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Renders a number input for a settings field.
 *
 * This function is used as a default callback when specifying 'number'
 * for the $callback parameter in `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 */
function render_settings_field_number( $field_args ) {
	$input_attrs = array(
		'type'  => 'number',
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
		'value' => ! empty( $field_args['value'] ) ? $field_args['value'] : '',
	);

	foreach ( array( 'min', 'max', 'step' ) as $attr ) {
		if ( isset( $field_args[ $attr ] ) ) {
			$input_attrs[ $attr ] = $field_args[ $attr ];
		}
	}

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
			$input_attrs['aria-describedby'] = $field_args['description_id'];
		}
		$description_attrs['class'] = 'description';
	}

	echo '<input' . attrs( $input_attrs, false ) . ' />';

	if ( ! empty( $field_args['description'] ) ) {
		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Renders a textarea input for a settings field.
 *
 * This function is used as a default callback when specifying 'textarea'
 * for the $callback parameter in `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 */
function render_settings_field_textarea( $field_args ) {
	$input_attrs = array(
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	if ( isset( $field_args['rows'] ) ) {
		$input_attrs['rows'] = $field_args['rows'];
	}

	if ( isset( $field_args['cols'] ) ) {
		$input_attrs['cols'] = $field_args['cols'];
	}

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
			$input_attrs['aria-describedby'] = $field_args['description_id'];
		}
		$description_attrs['class'] = 'description';
	}

	$value = ! empty( $field_args['value'] ) ? $field_args['value'] : '';

	echo '<textarea' . attrs( $input_attrs, false ) . '>' . esc_textarea( $value ) . '</textarea>';

	if ( ! empty( $field_args['description'] ) ) {
		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Renders a dropdown input for a settings field.
 *
 * This function is used as a default callback when specifying 'select'
 * for the $callback parameter in `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 */
function render_settings_field_select( $field_args ) {
	$input_attrs = array(
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	if ( ! empty( $field_args['multiple'] ) ) {
		if ( ! empty( $input_attrs['name'] ) ) {
			$input_attrs['name'] .= '[]';
		}
		$input_attrs['multiple'] = 'multiple';
	}

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
			$input_attrs['aria-describedby'] = $field_args['description_id'];
		}
		$description_attrs['class'] = 'description';
	}

	$choices = ! empty( $field_args['choices'] ) ? $field_args['choices'] : array();
	$current = ! empty( $field_args['value'] ) ? $field_args['value'] : '';
	if ( ! empty( $field_args['multiple'] ) ) {
		$current = ! empty( $current ) ? (array) $current : array();
	}

	echo '<select' . attrs( $input_attrs, false ) . '>';

	foreach ( $choices as $value => $label ) {
		$selected = '';
		if ( ! empty( $field_args['multiple'] ) && in_array( $value, $current ) ) {
			$selected = ' selected="selected"';
		} elseif ( empty( $field_args['multiple'] ) ) {
			$selected = selected( $current, $value, false );
		}
		echo '<option value="' . esc_attr( $value ) . '"' . $selected . '>' . esc_html( $label ) . '</option>';
	}

	echo '</select>';

	if ( ! empty( $field_args['description'] ) ) {
		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Renders a checkbox input for a settings field.
 *
 * This function is used as a default callback when specifying 'checkbox'
 * for the $callback parameter in `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 */
function render_settings_field_checkbox( $field_args ) {
	$input_attrs = array(
		'type'  => 'checkbox',
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
		'value' => '1',
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

	echo '<input' . attrs( $input_attrs, false ) . checked( $current, true, false ) . ' />';

	if ( ! empty( $field_args['label'] ) ) {
		if ( ! empty( $field_args['label_for'] ) ) {
			echo ' <label for="' . esc_attr( $field_args['label_for'] ) . '" class="title-label">' . $field_args['label'] . '</label>';
		} else {
			echo ' <span class="title-label">' . $field_args['label'] . '</span>';
		}
	}

	if ( ! empty( $field_args['description'] ) ) {
		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Renders a radio input for a settings field.
 *
 * This function is used as a default callback when specifying 'radio'
 * for the $callback parameter in `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 */
function render_settings_field_radio( $field_args ) {
	$input_attrs = array(
		'type'  => 'radio',
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	$choices = ! empty( $field_args['choices'] ) ? $field_args['choices'] : array();
	$current = ! empty( $field_args['value'] ) ? $field_args['value'] : '';

	$id_suffix = 0;
	foreach ( $choices as $value => $label ) {
		$id_suffix++;

		$radio_attrs = $input_attrs;
		$radio_attrs['id'] .= '-' . zeroise( $id_suffix, 2 );
		$radio_attrs['value'] = $value;

		echo '<span class="radio-item">';
		echo '<input' . attrs( $radio_attrs, false ) . checked( $current, $value, false ) . ' />';
		echo ' <label for="' . $radio_attrs['id'] . '">' . $label . '</label>';
		echo '</span><br />';
	}

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
		}

		$description_attrs['class'] = 'description';

		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Renders a multiple checkboxes input for a settings field.
 *
 * This function is used as a default callback when specifying 'multibox'
 * for the $callback parameter in `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 */
function render_settings_field_multibox( $field_args ) {
	$input_attrs = array(
		'type'  => 'checkbox',
		'id'    => ! empty( $field_args['input_id'] ) ? $field_args['input_id'] : '',
		'name'  => ! empty( $field_args['input_name'] ) ? $field_args['input_name'] . '[]' : '',
		'class' => ! empty( $field_args['input_class'] ) ? $field_args['input_class'] : '',
	);

	$choices = ! empty( $field_args['choices'] ) ? $field_args['choices'] : array();
	$current = ! empty( $field_args['value'] ) ? (array) $field_args['value'] : array();

	$id_suffix = 0;
	foreach ( $choices as $value => $label ) {
		$id_suffix++;

		$checkbox_attrs = $input_attrs;
		$checkbox_attrs['id'] .= '-' . zeroise( $id_suffix, 2 );
		$checkbox_attrs['value'] = $value;

		if ( in_array( $value, $current ) ) {
			$checkbox_attrs['checked'] = 'checked';
		}

		echo '<span class="multibox-item">';
		echo '<input' . attrs( $checkbox_attrs, false ) . ' />';
		echo ' <label for="' . $checkbox_attrs['id'] . '">' . $label . '</label>';
		echo '</span><br />';
	}

	$description_attrs = array();

	if ( ! empty( $field_args['description'] ) ) {
		if ( ! empty( $field_args['description_id'] ) ) {
			$description_attrs['id'] = $field_args['description_id'];
		}

		$description_attrs['class'] = 'description';

		echo '<p' . attrs( $description_attrs, false ) . '>' . $field_args['description'] . '</p>';
	}
}

/**
 * Creates an attribute string from an array of attributes.
 *
 * @since 4.8.0
 *
 * @param array $attrs Array of attributes as $key => $value pairs.
 * @param bool  $echo  Optional. Whether to echo the attribute string.
 *                     Default true.
 * @return string The attribute string.
 */
function attrs( $attrs, $echo = true ) {
	$attribute_string = '';

	foreach ( $attrs as $key => $value ) {
		$attribute_string .= ' ' . $key . '="' . esc_attr( $value ) . '"';
	}

	if ( $echo ) {
		echo $attribute_string;
	}

	return $attribute_string;
}

/**
 * Retrieves the value for a settings field, based on the field arguments.
 *
 * The function will call `get_option()` based on the 'input_name' argument
 * passed. It will automatically look for the correct key if an array option
 * is used for the field.
 *
 * This is the default callback function used when registering a field with
 * `add_settings_field()`.
 *
 * @since 4.8.0
 *
 * @param array $field_args Field arguments. See the documentation for the
 *                          $args parameter of `add_settings_field()` for a
 *                          list of default arguments.
 * @return mixed The value for the settings field, or null if no value set.
 */
function get_settings_field_option( $field_args ) {
	if ( empty( $field_args['input_name'] ) ) {
		return null;
	}

	$keys = preg_split( '/\[/', str_replace( ']', '', $field_args['input_name'] ) );
	$base = array_shift( $keys );

	$value = get_option( $base, null );
	while ( ! empty( $keys ) ) {
		$current_key = array_shift( $keys );
		if ( ! isset( $value[ $current_key ] ) ) {
			return null;
		}

		$value = $value[ $current_key ];
	}

	return $value;
}
