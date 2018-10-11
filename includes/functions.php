<?php

function wppeta_get_default_args() {

	$css_defaults = ".wppeta-thumbnail{\nwidth: 60px;\nheight: 60px;\n}";

	$defaults = array(

		// General tab
		'title'            => esc_html__( 'Recent Posts', 'PETA-recent-posts' ),
		'title_url'        => '',
		'css_class'        => '',
		'before'           => '',
		'after'            => '',

		// Posts tab
		'ignore_sticky'    => true,
		'exclude_current'  => true,
		'limit'            => 5,
		'offset'           => 0,
		'post_type'        => array( 'post' ),
		'post_status'      => 'publish',
		'order'            => 'DESC',
		'orderby'          => 'date',

		// Taxonomy tab
		'cat'              => array(),
		'tag'              => array(),
		'cat_exclude'      => array(),
		'tag_exclude'      => array(),

		// Thumbnail tab
		'thumbnail'         => true,
		'thumbnail_size'    => 'thumbnail',
		'thumbnail_default' => '//placehold.it/45x45/f0f0f0/ccc',
		'thumbnail_align'   => 'wppeta-alignleft',

		// Excerpt tab
		'excerpt'          => false,
		'length'           => 10,
		'readmore'         => false,
		'readmore_text'    => esc_html__( 'Read More &raquo;', 'PETA-recent-posts' ),

		// Display tab
		'post_title'       => true,
		'date'             => true,
		'date_relative'    => false,
		'date_modified'    => false,
		'comment_count'    => false,
		'author'           => false,

	);

	// Allow plugins/themes developer to filter the default arguments.
	return apply_filters( 'wppeta_default_args', $defaults );

}

/**
 * Outputs the recent posts.
 */
function wppeta_recent_posts( $args = array() ) {
	echo wppeta_get_recent_posts( $args );
}

/**
 * Generates the posts markup.
 */
function wppeta_get_recent_posts( $args = array() ) {

	// Set up a default, empty variable.
	$html = '';

	// Merge the input arguments and the defaults.
	$args = wp_parse_args( $args, wppeta_get_default_args() );

	// Extract the array to allow easy use of variables.
	extract( $args );

	// Allow devs to hook in stuff before the loop.
	do_action( 'wppeta_before_loop' );

	// Link target
	$target = '_self';
	if ( $args['new_tab'] ) {
		$target = '_blank';
	}

	// Get the posts query.
	$posts = wppeta_get_posts( $args );

	if ( $posts->have_posts() ) :

		// Recent posts wrapper
		$html = '<div class="wppeta-block wppeta-' . sanitize_html_class( $args['style'] ) . '-style ' . ( ! empty( $args['css_class'] ) ? '' . sanitize_html_class( $args['css_class'] ) . '' : '' ) . '">';

			// Custom CSS.
			if ( ! empty( $args['css'] ) ) {
				$html .= '<style>' . $args['css'] . '</style>';
			}

			$html .= '<ul class="wppeta-ul">';

				while ( $posts->have_posts() ) : $posts->the_post();

					// Start recent posts markup.
					$html .= '<li class="wppeta-li wppeta-clearfix">';

						if ( $args['thumbnail'] ) :

							// Check if post has post thumbnail.
							if ( has_post_thumbnail() ) :
								$html .= '<a class="wppeta-img ' . esc_attr( $args['thumbnail_align'] ) . '" href="' . esc_url( get_permalink() ) . '" target="' . $target . '">';
									$html .= get_the_post_thumbnail( get_the_ID(),
										$args['thumbnail_size'],
										array(
											'class' => ' wppeta-thumbnail',
											'alt'   => esc_attr( get_the_title() )
										)
									);
								$html .= '</a>';

							// Display default image.
							elseif ( ! empty( $args['thumbnail_default'] ) ) :
								$html .= sprintf( '<a class="wppeta-img ' . esc_attr( $args['thumbnail_align'] ) . '" href="%1$s" target="' . $target . '" rel="bookmark"><img class="wppeta-thumbnail wppeta-default-thumbnail" src="%2$s" alt="%3$s"></a>',
									esc_url( get_permalink() ),
									esc_url( $args['thumbnail_default'] ),
									esc_attr( get_the_title() )
								);

							endif;

						endif;

						$html .= '<div class="wppeta-content">';

							if ( $args['post_title'] ) :
								$html .= '<a class="wppeta-title" href="' . esc_url( get_permalink() ) . '" target="' . $target . '">' . esc_attr( get_the_title() ) . '</a>';
							endif;

							$html .= '<div class="wppeta-meta">';

								if ( $args['date'] ) :
									$date = get_the_date();
									if ( $args['date_relative'] ) :
										$date = sprintf( esc_html__( '%s ago', 'PETA-recent-posts' ), human_time_diff( get_the_date( 'U' ), current_time( 'timestamp' ) ) );
									endif;
									$html .= '<time class="wppeta-time published" datetime="' . esc_html( get_the_date( 'c' ) ) . '">' . esc_html( $date ) . '</time>';
								elseif ( $args['date_modified'] ) : // if both date functions are provided, we use date to be backwards compatible
									$date = get_the_modified_date();
									if ( $args['date_relative'] ) :
										$date = sprintf( esc_html__( '%s ago', 'PETA-recent-posts' ), human_time_diff( get_the_modified_date( 'U' ), current_time( 'timestamp' ) ) );
									endif;
									$html .= '<time class="wppeta-time modified" datetime="' . esc_html( get_the_modified_date( 'c' ) ) . '">' . esc_html( $date ) . '</time>';
								endif;

								if ( $args['comment_count'] ) :
									if ( get_comments_number() == 0 ) {
											$comments = esc_html__( 'No Comments', 'PETA-recent-posts' );
										} elseif ( get_comments_number() > 1 ) {
											$comments = sprintf( esc_html__( '%s Comments', 'PETA-recent-posts' ), get_comments_number() );
										} else {
											$comments = esc_html__( '1 Comment', 'PETA-recent-posts' );
										}
									$html .= '<a class="wppeta-comment comment-count" href="' . get_comments_link() . '" target="' . $target . '">' . $comments . '</a>';
								endif;

								if ( $args['author'] ) :
									$html .= '<a class="wppeta-author" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" target="' . $target . '">' . get_the_author() . '</a>';
								endif;

							$html .= "</div>";

							if ( $args['excerpt'] ) :
								$html .= '<div class="wppeta-summary">';
									$html .= '<p>' . wp_trim_words( apply_filters( 'wppeta_excerpt', get_the_excerpt() ), $args['length'] ) . '</p>';
									if ( $args['readmore'] ) :
										$html .= '<a href="' . esc_url( get_permalink() ) . '" class="wppeta-more-link" target="' . $target . '">' . $args['readmore_text'] . '</a>';
									endif;
								$html .= '</div>';
							endif;

						$html .= '</div>';

					$html .= '</li>';

				endwhile;

			$html .= '</ul>';

		$html .= '</div><!-- Generated by http://wordpress.org/plugins/PETA-recent-posts/ -->';

	endif;

	// Restore original Post Data.
	wp_reset_postdata();

	// Allow devs to hook in stuff after the loop.
	do_action( 'wppeta_after_loop' );

	// Return the  posts markup.
	return $args['before'] . apply_filters( 'wppeta_markup', $html ) . $args['after'];

}

/**
 * The posts query.
 */
function wppeta_get_posts( $args = array() ) {

	// Query arguments.
	$query = array(
		'offset'              => $args['offset'],
		'posts_per_page'      => $args['limit'],
		'orderby'             => $args['orderby'],
		'order'               => $args['order'],
		'post_type'           => $args['post_type'],
		'post_status'         => $args['post_status'],
		'ignore_sticky_posts' => $args['ignore_sticky'],
	);

	// Exclude current post
	if ( $args['exclude_current'] ) {
		$query['post__not_in'] = array( get_the_ID() );
	}

	// Include posts based on selected categories.
	if ( ! empty( $args['cat'] ) ) {
		$query['category__in'] = $args['cat'];
	}

	// Include posts based on selected post tags.
	if ( ! empty( $args['tag'] ) ) {
		$query['tag__in'] = $args['tag'];
	}

	// Exlucde posts based on selected categories.
	if ( ! empty( $args['cat_exclude'] ) ) {
		$query['category__not_in'] = $args['cat_exclude'];
	}

	// Exclude posts based on selected post tags.
	if ( ! empty( $args['tag_exclude'] ) ) {
		$query['tag__not_in'] = $args['tag_exclude'];
	}

	// Allow plugins/themes developer to filter the default query.
	$query = apply_filters( 'wppeta_default_query_arguments', $query );

	// Perform the query.
	$posts = new WP_Query( $query );

	return $posts;

}
