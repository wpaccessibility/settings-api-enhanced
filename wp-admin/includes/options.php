<?php
/**
 * WordPress Options Administration API.
 *
 * Adjusted prefixed variants of existing Core functions
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function sae_options_general_add_js() {
?>
<script type="text/javascript">
	jQuery( document ).ready(function( $ ) {
		var $siteName = $( '#wp-admin-bar-site-name' ).children( 'a' ).first(),
			homeURL = ( <?php echo wp_json_encode( get_home_url() ); ?> || '' ).replace( /^(https?:\/\/)?(www\.)?/, '' ),
			$dateTimeFormatRadios = $( '[name="date_format"], [name="time_format"]' ),
			$dateTimeCustomFormatRadios = $( '#date_format_custom_radio, #time_format_custom_radio' ),
			$dateTimeCustomFormatInputs = $( '#date_format_custom, #time_format_custom' ),
			$languageSelect = $( '#WPLANG' );

		$( '#blogname' ).on( 'input', function() {
			var title = $.trim( $( this ).val() ) || homeURL;

			// Truncate to 40 characters.
			if ( 40 < title.length ) {
				title = title.substring( 0, 40 ) + '\u2026';
			}

			$siteName.text( title );
		});

		/*
		 * When clicking on a date-time format radio button other than the custom
		 * one, update the custom format input and the example text.
		 */
		$dateTimeFormatRadios.not( $dateTimeCustomFormatRadios ).click( function() {
			var $setting = $( this ).closest( '.settings-field' );

			$setting.find( '[type="text"]' ).val( $( this ).val() );
			$setting.find( '.example' ).text(
				// Get the label associated to the radio button, and find the formatted date-time text.
				$( '[for="' + $( this ).attr( 'id' ) + '"]' ).find( '.format-i18n' ).text()
			);
		});

		// Get the date-time custom format input fields.
		$dateTimeCustomFormatInputs
			/*
			 * Check the custom date-time format radio button when clicking,
			 * typing, pasting in the custom format input field.
			 */
			.on( 'click input', function() {
				$( this ).closest( '.settings-field' ).find( $dateTimeCustomFormatRadios ).prop( 'checked', true );
			})
			/*
			 * When the custom format input field value changes, sanitize the
			 * input, show a spinner, and update the example text.
			 */
			.change( function() {
				var $format = $( this ),
					$setting = $format.closest( '.settings-field' ),
					$spinner = $setting.find( '.spinner' ),
					$example = $setting.find( '.example' );

				$spinner.addClass( 'is-active' );

				$.post( ajaxurl, {
						action: 'date_format_custom' == $format.attr( 'name' ) ? 'date_format' : 'time_format',
						date : $format.val()
					}, function( formattedDateTime ) {
						$spinner.removeClass( 'is-active' );
						$example.text( formattedDateTime );
					}
				);
			});

		$( 'form' ).submit( function() {
			// Don't show a spinner for English and installed languages,
			// as there is nothing to download.
			if ( ! $languageSelect.find( 'option:selected' ).data( 'installed' ) ) {
				$( '#submit', this ).after( '<span class="spinner language-install-spinner" />' );
			}
		});
	});
</script>
<?php
}

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function sae_options_reading_add_js() {
?>
<script type="text/javascript">
	jQuery( document ).ready( function( $ ){
		var section = $( '.js-front-static-pages' ),
			staticPage = section.find( 'input:radio[value="page"]' ),
			selects = section.find( 'select' ),
			check_disabled = function(){
				selects.prop( 'disabled', ! staticPage.prop( 'checked' ) );
			};
		check_disabled();
 		section.find( 'input:radio' ).change( check_disabled );
	});
</script>
<?php
}
