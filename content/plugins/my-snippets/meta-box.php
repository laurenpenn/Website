<?php

/* Create the post meta box. */
add_action( 'admin_menu', 'my_snippets_create_post_meta_box' );

/* Save the post metadata. */
add_action( 'save_post', 'my_snippets_save_post_meta_box', 10, 2 );

/**
 * Adds the meta boxes to the edit post/page screen in the WordPress admin. Users can also
 * filter the `edit_my_snippet_capability` to create a custom capability for adding My Snippetss.
 *
 * @since 0.1
 */
function my_snippets_create_post_meta_box() {
	global $theme_name;

	$capability = apply_filters( 'edit_snippet_capability', false );

	if (  ( !$capability ) || ( $capability && current_user_can( $capability ) ) ) {
		add_meta_box( 'my-snippets-post-meta-box', sprintf( __('Snippet %1$s settings', 'my-snippets'), 'post' ), 'my_snippets_post_meta_box', 'post', 'normal', 'high' );
		add_meta_box( 'my-snippets-page-meta-box', sprintf( __('Snippet %1$s settings', 'my-snippets'), 'page' ), 'my_snippets_post_meta_box', 'page', 'normal', 'high' );
	}
}

/**
 * Displays the post meta box, which includes the Snippet Title and Snippet Content fields.
 *
 * @since 0.1
 */
function my_snippets_post_meta_box() {
	global $post;

	$my_snippet_title = get_post_meta( $post->ID, 'Snippet Title', true );
	$my_snippet = get_post_meta( $post->ID, 'Snippet', true ); ?>

	<p>
		<label for="snippet-title"><?php _e('<strong>Snippet Title:</strong> (Overwrites the widget title.)', 'my-snippets'); ?></label>
		<br />
		<input type="text" name="snippet-title" id="snippet-title" value="<?php echo wp_specialchars( $my_snippet_title, 1 ); ?>" size="30" tabindex="30" style="width: 99%;" />
	</p>

	<p>
		<label for="snippet"><?php _e('<strong>Snippet:</strong> (Add text, <acronym title="Hypertext Markup Language">HTML</acronym>, and/or shortcodes.)', 'my-snippets'); ?></label>
		<br />
		<textarea name="snippet" id="snippet" cols="60" rows="4" style="width: 99%;"><?php echo wp_specialchars( $my_snippet, 1 ); ?></textarea>
		<input type="hidden" name="my_snippets_noncename" id="my_snippets_noncename" value="<?php echo wp_create_nonce( plugin_basename( __FILE__ ) ); ?>" />
	</p>

	<?php
}

/**
 * Saves the post metadata added through the My Snippetss settings fields.
 *
 * @since 0.1
 */
function my_snippets_save_post_meta_box( $post_id, $post ) {

	/* Only allow users that can edit the current post to submit data. */
	if ( 'post' == $post->post_type && !current_user_can( 'edit_post', $post_id ) )
		return;

	/* Only allow users that can edit the current page to submit data. */
	elseif ( 'page' == $post->post_type && !current_user_can( 'edit_page', $post_id ) )
		return;

	/* Don't save if the post is only a revision. */
	if ( 'revision' == $post->post_type )
		return;

	/* Verify the post form. */
	if ( !wp_verify_nonce( $_POST['my_snippets_noncename'], plugin_basename( __FILE__ ) ) )
		return $post_id;

	/* Loop through metadata, checking each option. */
	foreach ( array( 'snippet-title' => 'Snippet Title', 'snippet' => 'Snippet' ) as $key => $value ) {

		/* Current my_snippet. */
		$old_meta = get_post_meta( $post_id, $value, true );

		/* Posted my_snippet. */
		$new_meta = $_POST[$key];

		/* Only allow 'unfiltered_html' in the my_snippet for users that have that capability. */
		if ( !current_user_can( 'unfiltered_html' ) )
			$new_meta = stripslashes( wp_filter_post_kses( $new_meta ) );

		/* If there is no snippet but one posted, add it. */
		if ( $new_meta && '' == $old_meta )
			add_post_meta( $post_id, $value, $new_meta, true );

		/* If the new snippet is different than the old one, update it. */
		elseif ( $old_meta != $new_meta )
			update_post_meta( $post_id, $value, $new_meta );

		/* If the posted snippet is blank, delete the old my_snippet. */
		if ( '' == $new_meta && $old_meta )
			delete_post_meta( $post_id, $value );
	}
}

?>