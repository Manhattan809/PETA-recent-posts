<?php

class PETA_RECENT_POSTS_WIDGET extends WP_Widget {

	/**
	 * Sets up the widgets.
	 */
	public function __construct() {

		// Set up the widget options.
		$widget_options = array(
			'classname'   => 'widget_PETA_recent_entries PETA_recent_posts',
			'description' => __( 'PETA widget is an advanced widget that gives you full control over the output of the most recent publications from other Wodpress websites.', 'PETA-recent-posts' ),
			'customize_selective_refresh' => true
		);

		$control_options = array(
			'width'  => 450
		);

		// Create the widget.
		parent::__construct(
			'wppeta_widget',                                           // $this->id_base
			__( 'PETA Recent Posts', 'PETA-recent-posts' ), // $this->name
			$widget_options,                                         // $this->widget_options
			$control_options                                         // $this->control_options
		);

		$this->alt_option_name = 'widget_PETA_recent_entries';

	}

	

	
	// Outputs the widget based on the arguments input through the widget controls.
	
	public function widget( $args, $instance ) {
		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		// Get the recent posts
		$recent = wppeta_get_recent_posts( $request );

		if ( $recent ) {

			// Output the theme's $before_widget wrapper.
			echo $args['before_widget'];

			// If both title and title url is not empty, display it.
			if ( ! empty( $instance['title_url'] ) && ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . '<a href="' . esc_url( $instance['title_url'] ) . '" title="' . esc_attr( $instance['title'] ) . '">' . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . '</a>' . $args['after_title'];

			// If the title not empty, display it.
			} elseif ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $args['after_title'];
			}

			// Get the recent posts query.
			echo $recent;

			// Close the theme's widget wrapper.
			echo $args['after_widget'];

		}

	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 */
	public function update( $new_instance, $old_instance ) {

		// Validate post_type submissions
		$name = get_post_types( array( 'public' => true ), 'names' );
		$types = array();
		foreach( $new_instance['post_type'] as $type ) {
			if ( in_array( $type, $name ) ) {
				$types[] = $type;
			}
		}
		if ( empty( $types ) ) {
			$types[] = 'post';
		}

		$instance                     = $old_instance;

		// General tab
		$instance['title']            = sanitize_text_field( $new_instance['title'] );
		$instance['title_url']        = esc_url_raw( $new_instance['title_url'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['before'] = $new_instance['before'];
		} else {
			$instance['before'] = wp_kses_post( $new_instance['before'] );
		}
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['after'] = $new_instance['after'];
		} else {
			$instance['after'] = wp_kses_post( $new_instance['after'] );
		}
		$instance['css_class']        = sanitize_html_class( $new_instance['css_class'] );

		// Posts tab
		$instance['ignore_sticky']    = isset( $new_instance['ignore_sticky'] ) ? (bool) $new_instance['ignore_sticky'] : false;
		$instance['exclude_current']  = isset( $new_instance['exclude_current'] ) ? (bool) $new_instance['exclude_current'] : false;
		$instance['limit']            = intval( $new_instance['limit'] );
		$instance['order']            = esc_attr( $new_instance['order'] );
		$instance['orderby']          = esc_attr( $new_instance['orderby'] );
		$instance['post_status']      = esc_attr( $new_instance['post_status'] );


		// Thumbnail tab
		$instance['thumbnail']        = isset( $new_instance['thumbnail'] ) ? (bool) $new_instance['thumbnail'] : false;
		$instance['thumbnail_size']   = esc_attr( $new_instance['thumbnail_size'] );
		$instance['thumbnail_default'] = esc_url_raw( $new_instance['thumbnail_default'] );
		$instance['thumbnail_align']  = esc_attr( $new_instance['thumbnail_align'] );

		// Excerpt tab
		$instance['excerpt']          = isset( $new_instance['excerpt'] ) ? (bool) $new_instance['excerpt'] : false;
		$instance['length']           = intval( $new_instance['length'] );
		$instance['readmore']         = isset( $new_instance['readmore'] ) ? (bool) $new_instance['readmore'] : false;
		$instance['readmore_text']    = sanitize_text_field( $new_instance['readmore_text'] );

		// Display tab
		$instance['post_title']       = isset( $new_instance['post_title'] ) ? (bool) $new_instance['post_title'] : false;
		$instance['date']             = isset( $new_instance['date'] ) ? (bool) $new_instance['date'] : false;
		$instance['date_relative']    = isset( $new_instance['date_relative'] ) ? (bool) $new_instance['date_relative'] : false;
		$instance['date_modified']    = isset( $new_instance['date_modified'] ) ? (bool) $new_instance['date_modified'] : false;
		$instance['comment_count']    = isset( $new_instance['comment_count'] ) ? (bool) $new_instance['comment_count'] : false;
		$instance['author']           = isset( $new_instance['author'] ) ? (bool) $new_instance['author'] : false;

		return $instance;

	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 */
	public function form( $instance ) {

		// Merge the user-selected arguments with the defaults.
		$instance = wp_parse_args( (array) $instance, wppeta_get_default_args() );

		// Extract the array to allow easy use of variables.
		extract( $instance ); ?>

		<script>
			jQuery( document ).ready( function( $ ) {

				// Cache selector in a variable
				// to improve speed.
				var $tabs = $( '.wppeta-form-tabs' ),
				    $hor  = $( '.horizontal-tabs' );

				// Initialize the jQuery UI tabs
				$tabs.tabs({
					active   : $.cookie( 'activetab' ),
					activate : function( event, ui ){
						$.cookie( 'activetab', ui.newTab.index(),{
							expires : 10
						});
					}
				}).addClass( 'ui-tabs-vertical' );

				// Add custom class
				$tabs.closest( '.widget-inside' ).addClass( 'wppeta-bg' );

				// Initialize the jQuery UI tabs
				$hor.tabs().addClass( 'ui-tabs-horizontal' );

			});
		</script>

		<div class="wppeta-form-tabs">

			<ul class="wppeta-tabs">
				<li><a href="#tab-1"><?php esc_html_e( 'General', 'PETA-recent-posts' ); ?></a></li>
				<li><a href="#tab-2"><?php esc_html_e( 'Posts', 'PETA-recent-posts' ); ?></a></li>
				<li><a href="#tab-4"><?php esc_html_e( 'Thumbnail', 'PETA-recent-posts' ); ?></a></li>
				<li><a href="#tab-5"><?php esc_html_e( 'Excerpt', 'PETA-recent-posts' ); ?></a></li>
				<li><a href="#tab-6"><?php esc_html_e( 'Display', 'PETA-recent-posts' ); ?></a></li>
			</ul>

			<div class="wppeta-tabs-content">

				<div id="tab-1" class="wppeta-tab-content">

					<p>
						<label for="<?php echo $this->get_field_id( 'title' ); ?>">
							<?php esc_html_e( 'Title', 'PETA-recent-posts' ); ?>
						</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'title_url' ); ?>">
							<?php esc_html_e( 'Title URL', 'PETA-recent-posts' ); ?>
						</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'title_url' ); ?>" name="<?php echo $this->get_field_name( 'title_url' ); ?>" type="url" value="<?php echo esc_url( $instance['title_url'] ); ?>" />
					</p>

				</div><!-- #tab-1 -->

				<div id="tab-2" class="wppeta-tab-content">

					<p>
						<input class="checkbox" type="checkbox" <?php checked( $instance['exclude_current'], 1 ); ?> id="<?php echo $this->get_field_id( 'exclude_current' ); ?>" name="<?php echo $this->get_field_name( 'exclude_current' ); ?>" />
						<label for="<?php echo $this->get_field_id( 'exclude_current' ); ?>">
							<?php esc_html_e( 'Exclude current post', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'limit' ); ?>">
							<?php esc_html_e( 'Number of posts to show', 'PETA-recent-posts' ); ?>
						</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" step="1" min="-1" value="<?php echo (int)( $instance['limit'] ); ?>" />
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'post_status' ); ?>">
							<?php esc_html_e( 'Post Status', 'PETA-recent-posts' ); ?>
						</label>
						<select class="widefat" id="<?php echo $this->get_field_id( 'post_status' ); ?>" name="<?php echo $this->get_field_name( 'post_status' ); ?>" style="width:100%;">
							<?php foreach ( get_available_post_statuses() as $status_value => $status_label ) { ?>
								<option value="<?php echo esc_attr( $status_label ); ?>" <?php selected( $instance['post_status'], $status_label ); ?>><?php echo esc_html( ucfirst( $status_label ) ); ?></option>
							<?php } ?>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'order' ); ?>">
							<?php esc_html_e( 'Order', 'PETA-recent-posts' ); ?>
						</label>
						<select class="widefat" id="<?php echo $this->get_field_id( 'order' ); ?>" name="<?php echo $this->get_field_name( 'order' ); ?>" style="width:100%;">
							<option value="DESC" <?php selected( $instance['order'], 'DESC' ); ?>><?php esc_html_e( 'Descending', 'wppeta' ) ?></option>
							<option value="ASC" <?php selected( $instance['order'], 'ASC' ); ?>><?php esc_html_e( 'Ascending', 'wppeta' ) ?></option>
						</select>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'orderby' ); ?>">
							<?php esc_html_e( 'Orderby', 'PETA-recent-posts' ); ?>
						</label>
						<select class="widefat" id="<?php echo $this->get_field_id( 'orderby' ); ?>" name="<?php echo $this->get_field_name( 'orderby' ); ?>" style="width:100%;">
							<option value="ID" <?php selected( $instance['orderby'], 'ID' ); ?>><?php esc_html_e( 'ID', 'wppeta' ) ?></option>
							<option value="author" <?php selected( $instance['orderby'], 'author' ); ?>><?php esc_html_e( 'Author', 'wppeta' ) ?></option>
							<option value="title" <?php selected( $instance['orderby'], 'title' ); ?>><?php esc_html_e( 'Title', 'wppeta' ) ?></option>
							<option value="date" <?php selected( $instance['orderby'], 'date' ); ?>><?php esc_html_e( 'Date', 'wppeta' ) ?></option>
							<option value="modified" <?php selected( $instance['orderby'], 'modified' ); ?>><?php esc_html_e( 'Modified', 'wppeta' ) ?></option>
							<option value="rand" <?php selected( $instance['orderby'], 'rand' ); ?>><?php esc_html_e( 'Random', 'wppeta' ) ?></option>
							<option value="comment_count" <?php selected( $instance['orderby'], 'comment_count' ); ?>><?php esc_html_e( 'Comment Count', 'wppeta' ) ?></option>
							<option value="menu_order" <?php selected( $instance['orderby'], 'menu_order' ); ?>><?php esc_html_e( 'Menu Order', 'wppeta' ) ?></option>
						</select>
					</p>

				</div><!-- #tab-2 -->

				
				<div id="tab-4" class="wppeta-tab-content">

					<?php if ( current_theme_supports( 'post-thumbnails' ) ) { ?>

						<p>
							<input id="<?php echo $this->get_field_id( 'thumbnail' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail' ); ?>" type="checkbox" <?php checked( $instance['thumbnail'] ); ?> />
							<label for="<?php echo $this->get_field_id( 'thumbnail' ); ?>">
								<?php esc_html_e( 'Display Thumbnail', 'PETA-recent-posts' ); ?>
							</label>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>">
								<?php esc_html_e( 'Thumbnail Size ', 'PETA-recent-posts' ); ?>
							</label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'thumbnail_size' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_size' ); ?>" style="width:100%;">
								<?php foreach ( get_intermediate_image_sizes() as $size ) { ?>
									<option value="<?php echo esc_attr( $size ); ?>" <?php selected( $instance['thumbnail_size'], $size ); ?>><?php echo esc_html( $size ); ?></option>
								<?php }	?>
							</select>
						</p>

						<p>
							<label class="wppeta-block" for="<?php echo $this->get_field_id( 'thumbnail_align' ); ?>">
								<?php esc_html_e( 'Thumbnail Alignment', 'PETA-recent-posts' ); ?>
							</label>
							<select class="widefat" id="<?php echo $this->get_field_id( 'thumbnail_align' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_align' ); ?>">
								<option value="wppeta-alignleft" <?php selected( $instance['thumbnail_align'], 'wppeta-alignleft' ); ?>><?php esc_html_e( 'Left', 'PETA-recent-posts' ) ?></option>
								<option value="wppeta-alignright" <?php selected( $instance['thumbnail_align'], 'wppeta-alignright' ); ?>><?php esc_html_e( 'Right', 'PETA-recent-posts' ) ?></option>
								<option value="wppeta-aligncenter" <?php selected( $instance['thumbnail_align'], 'wppeta-aligncenter' ); ?>><?php esc_html_e( 'Center', 'PETA-recent-posts' ) ?></option>
							</select>
						</p>

						<p>
							<label for="<?php echo $this->get_field_id( 'thumbnail_default' ); ?>">
								<?php esc_html_e( 'Default Thumbnail', 'PETA-recent-posts' ); ?>
							</label>
							<input class="widefat" id="<?php echo $this->get_field_id( 'thumbnail_default' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_default' ); ?>" type="text" value="<?php echo $instance['thumbnail_default']; ?>"/>
							<small><?php esc_html_e( 'Leave it blank to disable.', 'PETA-recent-posts' ); ?></small>
						</p>

					<?php } ?>

				</div><!-- #tab-4 -->

				<div id="tab-5" class="wppeta-tab-content">

					<p>
						<input id="<?php echo $this->get_field_id( 'excerpt' ); ?>" name="<?php echo $this->get_field_name( 'excerpt' ); ?>" type="checkbox" <?php checked( $instance['excerpt'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'excerpt' ); ?>">
							<?php esc_html_e( 'Display Excerpt', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'length' ); ?>">
							<?php esc_html_e( 'Excerpt Length', 'PETA-recent-posts' ); ?>
						</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'length' ); ?>" name="<?php echo $this->get_field_name( 'length' ); ?>" type="number" step="1" min="0" value="<?php echo (int)( $instance['length'] ); ?>" />
					</p>

					<p>
						<input id="<?php echo $this->get_field_id( 'readmore' ); ?>" name="<?php echo $this->get_field_name( 'readmore' ); ?>" type="checkbox" <?php checked( $instance['readmore'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'readmore' ); ?>">
							<?php esc_html_e( 'Display Readmore', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<label for="<?php echo $this->get_field_id( 'readmore_text' ); ?>">
							<?php esc_html_e( 'Readmore Text', 'PETA-recent-posts' ); ?>
						</label>
						<input class="widefat" id="<?php echo $this->get_field_id( 'readmore_text' ); ?>" name="<?php echo $this->get_field_name( 'readmore_text' ); ?>" type="text" value="<?php echo strip_tags( $instance['readmore_text'] ); ?>" />
					</p>

				</div><!-- #tab-5 -->

				<div id="tab-6" class="wppeta-tab-content">

					<p>
						<input id="<?php echo $this->get_field_id( 'post_title' ); ?>" name="<?php echo $this->get_field_name( 'post_title' ); ?>" type="checkbox" <?php checked( $instance['post_title'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'post_title' ); ?>">
							<?php esc_html_e( 'Display Title', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<input id="<?php echo $this->get_field_id( 'date' ); ?>" name="<?php echo $this->get_field_name( 'date' ); ?>" type="checkbox" <?php checked( $instance['date'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'date' ); ?>">
							<?php esc_html_e( 'Display Date', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<input id="<?php echo $this->get_field_id( 'comment_count' ); ?>" name="<?php echo $this->get_field_name( 'comment_count' ); ?>" type="checkbox" <?php checked( $instance['comment_count'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'comment_count' ); ?>">
							<?php esc_html_e( 'Display Comment Count', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<input id="<?php echo $this->get_field_id( 'author' ); ?>" name="<?php echo $this->get_field_name( 'author' ); ?>" type="checkbox" <?php checked( $instance['author'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'author' ); ?>">
							<?php esc_html_e( 'Display Author', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<input id="<?php echo $this->get_field_id( 'date_modified' ); ?>" name="<?php echo $this->get_field_name( 'date_modified' ); ?>" type="checkbox" <?php checked( $instance['date_modified'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'date_modified' ); ?>">
							<?php esc_html_e( 'Display Modification Date', 'PETA-recent-posts' ); ?>
						</label>
					</p>

					<p>
						<input id="<?php echo $this->get_field_id( 'date_relative' ); ?>" name="<?php echo $this->get_field_name( 'date_relative' ); ?>" type="checkbox" <?php checked( $instance['date_relative'] ); ?> />
						<label for="<?php echo $this->get_field_id( 'date_relative' ); ?>">
							<?php esc_html_e( 'Use Relative Date. eg: 5 days ago', 'PETA-recent-posts' ); ?>
						</label>
					</p>

				</div><!-- #tab-6 -->

			</div><!-- .wppeta-tabs-content -->

		</div><!-- .wppeta-form-tabs -->

	<?php
	}

}
