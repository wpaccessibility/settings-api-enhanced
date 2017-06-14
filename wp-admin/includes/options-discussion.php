<?php
/**
 * WordPress Options implementation for Discussion Settings.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.8.0
 */

/**
 * Adds default settings fields for the Discussion Settings page.
 *
 * @since 4.8.0
 */
function add_settings_fields_options_discussion() {
	sae_add_settings_section( 'article', __( 'Default article settings' ), 'settings_section_article_before', 'discussion' );

	sae_add_settings_field( 'default_pingback_flag', '', 'checkbox', 'discussion', 'article', array(
		'skip_title' => true,
		'label'      => __( 'Attempt to notify any blogs linked to from the article' ),
	) );

	sae_add_settings_field( 'default_ping_status', '', 'checkbox', 'discussion', 'article', array(
		'skip_title' => true,
		'label'      => __( 'Allow link notifications from other blogs (pingbacks and trackbacks) on new articles' ),
	) );

	sae_add_settings_field( 'default_comment_status', '', 'checkbox', 'discussion', 'article', array(
		'skip_title' => true,
		'label'      => __( 'Allow people to post comments on new articles' ),
	) );

	sae_add_settings_section( 'comment', __( 'Other comment settings' ), null, 'discussion' );

	sae_add_settings_field( 'require_name_email', '', 'checkbox', 'discussion', 'comment', array(
		'skip_title' => true,
		'label'      => __( 'Comment author must fill out name and email' ),
	) );

	if ( ! get_option( 'users_can_register' ) && is_multisite() ) {
		sae_add_settings_field( 'comment_registration', '', 'checkbox', 'discussion', 'comment', array(
			'skip_title'  => true,
			'label'       => __( 'Users must be registered and logged in to comment' ),
			'description' => __( 'Signup has been disabled. Only members of this site can comment.' ),
		) );
	} else {
		sae_add_settings_field( 'comment_registration', '', 'checkbox', 'discussion', 'comment', array(
			'skip_title' => true,
			'label'      => __( 'Users must be registered and logged in to comment' ),
		) );
	}

	sae_add_settings_field( 'close_comments_for_old_posts', '', 'checkbox', 'discussion', 'comment', array(
		'skip_title' => true,
		'label'      => __( 'Automatically close comments on old articles: enter the number of days in the field below' ),
	) );

	sae_add_settings_field( 'close_comments_days_old', __( 'Number of days after which comments will be automatically closed' ), 'number', 'discussion', 'comment', array(
		'input_class' => 'small-text',
		'min'         => '0',
		'step'        => '1',
	) );

	sae_add_settings_field( 'thread_comments', '', 'checkbox', 'discussion', 'comment', array(
		'skip_title' => true,
		'label'      => __( 'Enable threaded (nested) comments: set the number of levels in the field below' ),
	) );

	/**
	 * Filters the maximum depth of threaded/nested comments.
	 *
	 * @since 2.7.0.
	 *
	 * @param int $max_depth The maximum depth of threaded comments. Default 10.
	 */
	$maxdeep = (int) apply_filters( 'thread_comments_depth_max', 10 );

	for ( $depth_index = 2; $depth_index <= $maxdeep; $depth_index++ ) {
		$thread_comments_depth_choices[ $depth_index ] = $depth_index;
	}

	sae_add_settings_field( 'thread_comments_depth', __( 'Nested comments levels deep' ), 'select', 'discussion', 'comment', array(
		'choices' => $thread_comments_depth_choices,
	) );

	sae_add_settings_field( 'page_comments', '', 'checkbox', 'discussion', 'comment', array(
		'skip_title' => true,
		'label'      => __( 'Break comments into pages' ),
	) );

	sae_add_settings_field( 'comments_per_page', __( 'Number of top level comments per page' ), 'number', 'discussion', 'comment', array(
		'input_class' => 'small-text',
		'min'         => '0',
		'step'        => '1',
	) );

	sae_add_settings_field( 'default_comments_page', __( 'Comments page displayed by default' ), 'select', 'discussion', 'comment', array(
		'choices' => array(
			'newest' => __( 'last page' ),
			'oldest' => __( 'first page' ),
		)
	) );

	sae_add_settings_field( 'comment_order', __( 'Comments to display at the top of each page' ), 'select', 'discussion', 'comment', array(
		'choices' => array(
			'asc'  => __( 'older comments' ),
			'desc' => __( 'newer comments' ),
		)
	) );

	sae_add_settings_section( 'notifications', __( 'Notifications' ), null, 'discussion' );

	sae_add_settings_field( 'comments_notify', '', 'checkbox', 'discussion', 'notifications', array(
		'skip_title' => true,
		'label'      => __( 'Email me when anyone posts a comment' ),
	) );

	sae_add_settings_field( 'moderation_notify', '', 'checkbox', 'discussion', 'notifications', array(
		'skip_title' => true,
		'label'      => __( 'Email me when a comment is held for moderation' ),
	) );

	sae_add_settings_section( 'moderation', __( 'Comment moderation' ), null, 'discussion' );

	sae_add_settings_field( 'comment_moderation', '', 'checkbox', 'discussion', 'moderation', array(
		'skip_title' => true,
		'label'      => __( 'Before a comment appears, it must be manually approved' ),
	) );

	sae_add_settings_field( 'comment_whitelist', '', 'checkbox', 'discussion', 'moderation', array(
		'skip_title' => true,
		'label'      => __( 'Before a comment appears, the comment author must have a previously approved comment' ),
	) );

	sae_add_settings_field( 'comment_max_links', __( 'Number of links in a comment after which it will be held in the moderation queue' ), 'number', 'discussion', 'moderation', array(
		'input_class' => 'small-text',
		'min'         => '0',
		'step'        => '1',
		'description' => __( 'A common characteristic of comment spam is a large number of links.' ),
	) );

	sae_add_settings_field( 'moderation_keys', __( 'Moderation words' ), 'textarea', 'discussion', 'moderation', array(
		'rows'        => '10',
		'cols'        => '50',
		'input_class' => 'large-text code',
		'description' => sprintf(
			__( 'When a comment contains any of these words in its content, name, URL, email, or IP, it will be held in the %1$smoderation queue%2$s. One word or IP per line. It will match inside words, so &#8220;press&#8221; will match &#8220;WordPress&#8221;.' ),
			'<a href="edit-comments.php?comment_status=moderated">',
			'</a>'
		),
	) );

	sae_add_settings_field( 'blacklist_keys', __( 'Comment blacklist' ), 'textarea', 'discussion', 'moderation', array(
		'rows'        => '10',
		'cols'        => '50',
		'input_class' => 'large-text code',
		'description' => __( 'When a comment contains any of these words in its content, name, URL, email, or IP, it will be put in the trash. One word or IP per line. It will match inside words, so &#8220;press&#8221; will match &#8220;WordPress&#8221;.' )
	) );
}

/**
 * Settings section callback for the 'thumbnail_size' section.
 *
 * @since 4.8.0
 */
function settings_section_article_before() {
	echo '<p>' . __( 'These settings may be overridden for individual articles.' ) . '</p>';
}
