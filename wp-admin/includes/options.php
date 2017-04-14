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
	jQuery( document ).ready( function( $ ) {
		var $siteName = $( '#wp-admin-bar-site-name' ).children( 'a' ).first(),
			homeURL = ( <?php echo wp_json_encode( get_home_url() ); ?> || '' ).replace( /^(https?:\/\/)?(www\.)?/, '' ),
			$languageSelect = $( '#WPLANG' );

		// Live update the admin bar site name while editing the Site Title field.
		$( '#blogname' ).on( 'input', function() {
			var title = $.trim( $( this ).val() ) || homeURL;

			// Truncate to 40 characters and append ellipsis.
			if ( 40 < title.length ) {
				title = title.substring( 0, 40 ) + '\u2026';
			}

			$siteName.text( title );
		});

		/*
		 * When clicking on a date-time format radio button other than the custom
		 * one, update the custom format input and the example text.
		 */
		$( '.js-date-time-format' ).not( '.js-date-time-custom-format-radio' ).click( function() {
			var $parent = $( this ).closest( '.setting' );

			$parent.find( '.js-date-time-custom-format-input' ).val( $( this ).val() );
			$parent.find( '.example' ).text(
					// Get the label associated to the radio button, and find the formatted date-time text.
					$( '[for="' + $( this ).attr( 'id' ) + '"]' ).find( '.format-i18n' ).text()
				);
		});

		// Get the date-time custom format input fields.
		$( '.js-date-time-custom-format-input' )
			/*
			 * Check the custom date-time format radio button when clicking,
			 * typing, pasting in the custom format input field.
			 */
			.on( 'click input', function() {
				$( this ).closest( '.setting').find( '.js-date-time-custom-format-radio' ).prop( 'checked', true );
			})
			/*
			 * When the custom format input field value changes, sanitize the
			 * input show a spinner, and update the example text.
			 */
			.change( function() {
				var $format = $( this ),
					$parent = $format.parent(),
					$spinner = $parent.find( '.js-date-time-custom-spinner' ),
					$example = $parent.find( '.example' );

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

		/*
		 * When submitting the form, show a spinner only if the selected language
		 * is not installed yet, as for English and already installed languages
		 * there is nothing to download.
		 */
		$( 'form' ).submit( function() {
			if ( ! $languageSelect.find( 'option:selected' ).data( 'installed' ) ) {
				$( '#submit', this ).after( '<span class="spinner language-install-spinner is-active" />' );
			}
		});
	});
</script>
<?php
}
