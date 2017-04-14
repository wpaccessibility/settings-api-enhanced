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
			homeURL = ( <?php echo wp_json_encode( get_home_url() ); ?> || '' ).replace( /^(https?:\/\/)?(www\.)?/, '' );

		$( '#blogname' ).on( 'input', function() {
			var title = $.trim( $( this ).val() ) || homeURL;

			// Truncate to 40 characters.
			if ( 40 < title.length ) {
				title = title.substring( 0, 40 ) + '\u2026';
			}

			$siteName.text( title );
		});

		$( 'input[name="date_format"]' ).click( function() {
			if ( 'date_format_custom_radio' !== $( this ).attr( 'id' ) ) {
				$( 'input[name="date_format_custom"]' )
					.val( $( this ).val() )
					.parent().find( '.example' ).text(
						$( this ).parent().find( '.format-i18n' ).text()
					);
			}
		});

		$( 'input[name="date_format_custom"]' ).focus( function() {
			$( '#date_format_custom_radio' ).prop( 'checked', true );
		});

		$( 'input[name="time_format"]' ).click( function() {
			if ( 'time_format_custom_radio' != $( this).attr( 'id' ) ) {
				$( 'input[name="time_format_custom"]' )
					.val( $( this ).val() )
					.parent().find( '.example' ).text(
						$( this ).parent().find( '.format-i18n' ).text()
					);
			}
		});

		$( 'input[name="time_format_custom"]' ).focus( function() {
			$( '#time_format_custom_radio' ).prop( 'checked', true );
		});

		$( 'input[name="date_format_custom"], input[name="time_format_custom"]' ).change( function() {
			var $format = $( this ),
				$parent = $format.parent(),
				$spinner = $parent.find( '.js-date-time-custom-spinner' ),
				$example = $parent.find( '.example' );

			$spinner.addClass( 'is-active' );

			$.post( ajaxurl, {
					action: 'date_format_custom' == $format.attr( 'name' ) ? 'date_format' : 'time_format',
					date : $format.val()
				}, function( dateOrTimeFormat ) {
					$spinner.removeClass( 'is-active' );
					$example.text( dateOrTimeFormat );
				}
			);
		});

		var languageSelect = $( '#WPLANG' );

		$( 'form' ).submit( function() {
			// Don't show a spinner for English and installed languages,
			// as there is nothing to download.
			if ( ! languageSelect.find( 'option:selected' ).data( 'installed' ) ) {
				$( '#submit', this ).after( '<span class="spinner language-install-spinner" />' );
			}
		});
	});
</script>
<?php
}
