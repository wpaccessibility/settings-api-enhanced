<?php
/**
 * Discussion settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
//require_once( dirname( __FILE__ ) . '/admin.php' );


/** WordPress Options implementation for Discussion Settings */
require_once( SAE_ABSPATH . 'wp-admin/includes/options-discussion.php' );

add_settings_fields_options_discussion();

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( __( 'Sorry, you are not allowed to manage options for this site.' ) );
}

$title = __( 'Discussion Settings' );
$parent_file = 'options-general.php';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => '<p>' . __( 'This screen provides many options for controlling the management and display of comments and links to your posts/pages. So many, in fact, they won&#8217;t all fit here! :) Use the documentation links to get information on what each discussion setting does.' ) . '</p>' .
		'<p>' . __( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.' ) . '</p>',
));

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://codex.wordpress.org/Settings_Discussion_Screen">Documentation on Discussion Settings</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support Forums</a>' ) . '</p>'
);

include( ABSPATH . 'wp-admin/admin-header.php' );
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<form method="post" action="options.php">
<?php settings_fields( 'discussion' ); ?>

<?php sae_do_settings_sections( 'discussion' ); ?>

<?php submit_button(); ?>
</form>
</div>
<?php include( ABSPATH . 'wp-admin/admin-footer.php' ); ?>
