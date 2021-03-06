<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Zero
 * @since 0.1.0
 * @version 0.2.0
 */

/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function zero_posted_on() {
	// Get the author name; wrap it in a link.
	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'zero' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);

	// Finally, let's write all of this to the page.
	echo '<span class="posted-on">' . zero_time_link() . '</span><span class="byline"> ' . $byline . '</span>';
}

/**
 * Gets a nicely formatted string for the published date.
 */
function zero_time_link() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		get_the_date( DATE_W3C ),
		get_the_date(),
		get_the_modified_date( DATE_W3C ),
		get_the_modified_date()
	);

	// Wrap the time string in a link, and preface it with 'Posted on'.
	return sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'zero' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);
}

/**
 * This template tag is meant to replace template tags like `the_category()`, `the_terms()`, etc.  These core
 * WordPress template tags don't offer proper translation and RTL support without having to write a lot of
 * messy code within the theme's templates.  This is why theme developers often have to resort to custom
 * functions to handle this (even the default WordPress themes do this). Particularly, the core functions
 * don't allow for theme developers to add the terms as placeholders in the accompanying text (ex: "Posted in %s").
 * This funcion is a wrapper for the WordPress `get_the_terms_list()` function.  It uses that to build a
 * better post terms list.
 *
 * @author  Justin Tadlock
 * @link    https://github.com/justintadlock/hybrid-core/blob/2.0/functions/template-post.php
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * @since   0.1.0
 * @param   array $args Options for Taxonomy.
 * @return  string
 */
function zero_get_post_terms( $args = array() ) {

	$html = '';

	$defaults = array(
		'post_id'    => get_the_ID(),
		'taxonomy'   => 'category',
		'text'       => '%s',
		'before'     => '',
		'after'      => '',
		'items_wrap' => '<span %s>%s</span>',
		/* Translators: Separates tags, categories, etc. when displaying a post. */
		'sep'        => '<span class="screen-reader-text">' . esc_html_x( ', ', 'taxonomy terms separator', 'zero' ) . '</span>',
	);

	$args = wp_parse_args( $args, $defaults );

	$terms = get_the_term_list( $args['post_id'], $args['taxonomy'], '', $args['sep'], '' );

	if ( ! empty( $terms ) ) {
		$html .= $args['before'];
		$html .= sprintf( $args['items_wrap'], 'class="entry-terms ' . $args['taxonomy'] . '"', sprintf( $args['text'], $terms ) );
		$html .= $args['after'];
	}

	return $html;
}

/**
 * Outputs a post's taxonomy terms.
 *
 * @since  0.1.0
 * @access public
 * @param  array $args Options for Taxonomy.
 * @return void
 */
function zero_post_terms( $args = array() ) {
	echo zero_get_post_terms( $args );
}

/**
 * Displays the optional custom logo.
 *
 * Does nothing if the custom logo is not available.
 */
function zero_the_custom_logo() {

	if ( function_exists( 'the_custom_logo' ) ) :
		the_custom_logo();
	endif;

}

/**
 * Displays posts pagination.
 *
 * Uses WordPress native the_posts_pagination function.
 */
function zero_posts_pagination() {
	the_posts_pagination( array(
		'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous page', 'zero' ) . '</span>' . zero_get_svg( array( 'icon' => 'arrow-circle-left' ) ),
		'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'zero' ) . '</span>' . zero_get_svg( array( 'icon' => 'arrow-circle-right' ) ),
		'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'zero' ) . ' </span>',
	) );
}

/**
 * Displays page-links for paginated posts
 *
 * Uses WordPress native wp_link_pages function.
 */
function zero_link_pages() {
	wp_link_pages( array(
		'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'zero' ),
		'after'  => '</div>',
		'link_before' => '<span>',
		'link_after' => '</span>',
		'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'zero' ) . ' </span>%',
		'separator'   => '<span class="screen-reader-text">, </span>',
	) );
}


/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function zero_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'zero_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'zero_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so zero_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so zero_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in zero_categorized_blog.
 */
function zero_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'zero_categories' );
}
add_action( 'edit_category', 'zero_category_transient_flusher' );
add_action( 'save_post',     'zero_category_transient_flusher' );
