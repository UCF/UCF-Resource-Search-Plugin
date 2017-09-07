<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF_Resource_Search_Common' ) ) {
	class UCF_Resource_Search_Common {

		public static function enqueue_styles() {
			if ( get_option( 'ucf_resource_search_include_css' ) ) {
				wp_enqueue_style( 'ucf_resource_search_css', plugins_url( 'static/css/ucf-resource-search.min.css', UCF_Resource_Search__PLUGIN_FILE ), false, false, 'all' );
			}
		}

		public static function enqueue_scripts() {
			wp_enqueue_script( 'ucf-resource_search_js', plugins_url( 'static/js/ucf-resource-search.min.js', UCF_Resource_Search__PLUGIN_FILE ), null, null, true );
		}

		/**
		 * Displays the output of the resource search content.
		 *
		 * @author R.J. Bruneel
		 * @since 1.0.0
		 *
		 * @param $params Array | An array of attributes.
		 *
		 * @return string | The output of the resource search content.
		 **/
		public static function display_resource_search( $params ) {

			$params['show_empty_sections'] = filter_var( $params['show_empty_sections'], FILTER_VALIDATE_BOOLEAN );
			$params['column_count']        = is_numeric( $params['column_count'] ) ? (int)$params['column_count'] : $defaults['column_count'];
			$params['show_sorting']        = filter_var( $params['show_sorting'], FILTER_VALIDATE_BOOLEAN );

			if ( !in_array( $params['default_sorting'], array( 'term', 'alpha' ) ) ) {
				$params['default_sorting'] = $default['default_sorting'];
			}

			$labels = UCF_Resource_Link_PostType::get_labels();

			// Set default search text if the user didn't
			if ( !isset( $params['default_search_text'] ) ) {
				$params['default_search_text'] = 'Find a '.$labels['singular'];
			}

			// Set default search label if the user didn't
			if ( !isset( $params['default_search_label'] ) ) {
				$params['default_search_label'] = 'Find a '.$labels['singular'];
			}

			// Register the search data with the JS PostTypeSearchDataManager.
			// Format is array(post->ID=>terms) where terms include the post title
			// as well as all associated tag names
			$search_data = array();
			foreach ( get_posts( array( 'numberposts' => -1, 'post_type' => $params['post_type_name'] ) ) as $post ) {
				$search_data[$post->ID] = array( $post->post_title );
				foreach ( wp_get_object_terms( $post->ID, 'post_tag' ) as $term ) {
					$search_data[$post->ID][] = $term->name;
				}
			}

			$before = self::ucf_resource_search_display_before( $resource = null );
			if ( has_filter( 'ucf_resource_search_display_before' ) ) {
				$before = apply_filters( 'ucf_resource_search_display_before', $output, $resource );
			}

			$content = self::ucf_resource_search_display( $params, $search_data );
			if ( has_filter( 'ucf_resource_search_display' ) ) {
				$content = apply_filters( 'ucf_resource_search_display', $output, $params );
			}

			$after = self::ucf_resource_search_display_after( $resource = null );
			if ( has_filter( 'ucf_resource_search_display_after' ) ) {
				$after = apply_filters( 'ucf_resource_search_display_after', $output, $resource );
			}

			$retval = $before . $content . $after;

			return $retval;
		}

		/**
		 * Prepends the resource search content with a resource_search tag.
		 * Use the `ucf_resource_search_display_before` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $resource WP_Post object | The resource
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_resource_search_display_before( $resource ) {
			ob_start();
		?>
			<article>
		<?php
			return ob_get_clean();
		}

		/**
		 * Outputs the content of the resource.
		 * Use the `ucf_resource_search_display` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $resource WP_Post object | The resource
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_resource_search_display( $params, $search_data ) {
			ob_start();
			?>

			<script type="text/javascript">
				if (typeof jQuery !== 'undefined') {
					jQuery(document).ready(function ($) {
						PostTypeSearchDataManager.register(new PostTypeSearchData(
							<?php echo json_encode( $params['column_count'] ); ?>,
							<?php echo json_encode( $params['column_width'] ); ?>,
							<?php echo json_encode( $search_data ); ?>
						));
					});
				} else {
					console.log('jQuery dependency failed to load');
				}
			</script>

			<?php

			// Set up a post query
			$args = array(
				'numberposts' => -1,
				'post_type'   => $params['post_type_name'],
				'tax_query'   => array(
					array(
						'taxonomy' => $params['taxonomy'],
						'field'    => 'id',
						'terms'    => '',
					)
				),
				'orderby'     => $params['order_by'],
				'order'       => $params['order'],
			);
			// Handle meta key and value query
			if ($params['meta_key'] && $params['meta_value']) {
				$args['meta_key'] = $params['meta_key'];
				$args['meta_value'] = $params['meta_value'];
			}
			// Split up this post type's posts by term
			$by_term = array();
			foreach ( get_terms( $params['taxonomy'] ) as $term ) { // get_terms defaults to an orderby=name, order=asc value
				$args['tax_query'][0]['terms'] = $term->term_id;
				$posts = get_posts( $args );
				if ( count( $posts ) == 0 && $params['show_empty_sections'] ) {
					$by_term[$term->name] = array();
				} else {
					$by_term[$term->name] = $posts;
				}
			}
			// Add uncategorized items to posts by term if parameter is set.
			if ( $params['show_uncategorized'] ) {
				$terms = get_terms( $params['taxonomy'], array( 'fields' => 'ids', 'hide_empty' => false ) );
				$args['tax_query'][0]['terms'] = $terms;
				$args['tax_query'][0]['operator'] = 'NOT IN';
				$uncat_posts = get_posts( $args );
				if ( count( $uncat_posts == 0 ) && $params['show_empty_sections'] ) {
					$by_term[$params['uncategorized_term_name']] = array();
				} else {
					$by_term[$params['uncategorized_term_name']] = $uncat_posts;
				}
			}
			// Split up this post type's posts by the first alpha character
			$args['orderby'] = 'title';
			$args['order'] = 'ASC';
			$args['tax_query'] = '';
			$by_alpha_posts = get_posts( $args );
			foreach( $by_alpha_posts as $post ) {
				if ( preg_match( '/([a-zA-Z])/', $post->post_title, $matches ) == 1 ) {
					$by_alpha[strtoupper($matches[1])][] = $post;
				} else {
					$by_alpha[$params['non_alpha_section_name']][] = $post;
				}
			}
			if( $params['show_empty_sections'] ) {
				foreach( range( 'a', 'z' ) as $letter ) {
					if ( !isset( $by_alpha[strtoupper( $letter )] ) ) {
						$by_alpha[strtoupper( $letter )] = array();
					}
				}
			}
			ksort( $by_alpha );
			$sections = array(
				'resource-search-term'  => $by_term,
				'resource-search-alpha' => $by_alpha,
			);
			ob_start();
		?>
			<div class="resource-search">
				<div class="resource-search-header">
					<form class="resource-search-form" action="." method="get">
						<div class="form-group">
							<label class="resource-search-label" for="resource-search-input">
								<h2>
									<?php echo $params['default_search_label']; ?>
								</h2>
							</label>
							<input type="text" id="resource-search-input" name="resource-search-input" class="form-control resource-search-input" placeholder="<?php echo $params['default_search_text']; ?>">
						</div>
					</form>
				</div>
				<div class="resource-search-results"></div>
				<?php if ( $params['show_sorting'] ) { ?>
				<div class="btn-group resource-search-sorting">
					<button class="btn btn-default<?php if ( $params['default_sorting'] == 'term' ) echo ' active'; ?>">
					</button>
					<button class="btn btn-default<?php if ( $params['default_sorting'] == 'alpha' ) echo ' active'; ?>">
					</button>
				</div>
				<?php } ?>

				<div class="btn-toolbar jump-to-list" role="toolbar" aria-label="Jump To List">
					<div class="btn-group" role="group">
						<a href="#jump-to-a" class="btn btn-default">A</a>
						<a href="#jump-to-b" class="btn btn-default">B</a>
						<a href="#jump-to-c" class="btn btn-default">C</a>
						<a href="#jump-to-d" class="btn btn-default">D</a>
						<a href="#jump-to-e" class="btn btn-default">E</a>
						<a href="#jump-to-f" class="btn btn-default">F</a>
						<a href="#jump-to-g" class="btn btn-default">G</a>
						<a href="#jump-to-h" class="btn btn-default">H</a>
						<a href="#jump-to-i" class="btn btn-default">I</a>
						<a href="#jump-to-j" class="btn btn-default">J</a>
						<a href="#jump-to-k" class="btn btn-default">K</a>
						<a href="#jump-to-l" class="btn btn-default">L</a>
						<a href="#jump-to-m" class="btn btn-default">M</a>
					</div>
					<div class="btn-group" role="group">
						<a href="#jump-to-n" class="btn btn-default">N</a>
						<a href="#jump-to-o" class="btn btn-default">O</a>
						<a href="#jump-to-p" class="btn btn-default">P</a>
						<a href="#jump-to-q" class="btn btn-default">Q</a>
						<a href="#jump-to-r" class="btn btn-default">R</a>
						<a href="#jump-to-s" class="btn btn-default">S</a>
						<a href="#jump-to-t" class="btn btn-default">T</a>
						<a href="#jump-to-u" class="btn btn-default">U</a>
						<a href="#jump-to-v" class="btn btn-default">V</a>
						<a href="#jump-to-w" class="btn btn-default">W</a>
						<a href="#jump-to-x" class="btn btn-default">X</a>
						<a href="#jump-to-y" class="btn btn-default">Y</a>
						<a href="#jump-to-z" class="btn btn-default">Z</a>
					</div>
				</div>

			<?php
			foreach ( $sections as $id => $section ):
				$hide = false;
				switch ( $id ) {
					case 'resource-search-alpha':
						if ( $params['default_sorting'] == 'term' ) {
							$hide = True;
						}
						break;
					case 'resource-search-term':
						if ( $params['default_sorting'] == 'alpha' ) {
							$hide = True;
						}
						break;
				}
		?>
				<div class="<?php echo $id; ?>"<?php if ( $hide ) { echo ' style="display:none;"'; } ?>>
					<div class="row">
					<?php
					$count = 0;
					foreach ( $section as $section_title => $section_posts ):
						if ( count( $section_posts ) > 0 || $params['show_empty_sections'] ):
					?>

						<?php if ( $section_title == $params['uncategorized_term_name'] ): ?>
							</div>
								<div class="row">
									<div class="<?php echo $params['column_width']; ?>">
										<div class="resource-search-heading-wrap" id="jump-to-<?php echo strtolower( esc_html( $section_title ) ); ?>">
											<h3 class="resource-search-heading"><?php echo esc_html( $section_title ); ?></h3>
											<hr>
										</div>
									</div>
								</div>

								<div class="row">
								<?php
								// $split_size must be at least 1
								$split_size = max( floor( count( $section_posts ) / $params['column_count'] ), 1 );
								$split_posts = array_chunk( $section_posts, $split_size );
								foreach ( $split_posts as $resource => $column_posts ):
								?>
									<div class="<?php echo $params['column_width']; ?>">
										<ul class="resource-search-list">
										<?php foreach( $column_posts as $key => $post ): ?>
											<li data-post-id="<?php echo $post->ID; ?>">
												<?php echo UCF_Resource_Link_PostType::toHTML( $post ); ?><?php echo $section_title; ?>
											</li>
										<?php endforeach; ?>
										</ul>
									</div>
								<?php endforeach; ?>

						<?php else: ?>

							<?php if ( $count % $params['column_count'] == 0 && $count !== 0 ): ?>
								</div><div class="row">
							<?php endif; ?>

							<div class="<?php echo $params['column_width']; ?>">
								<div class="resource-search-heading-wrap" id="jump-to-<?php echo strtolower( esc_html( $section_title ) ); ?>">
									<h3 class="resource-search-heading"><?php echo esc_html( $section_title ); ?></h3>
									<div class="back-to-top"><a href="#top">Back to Top</a></div></div>
								<hr>
								<ul class="resource-search-list">
								<?php foreach( $section_posts as $post ): ?>
									<li data-post-id="<?php echo $post->ID; ?>">
										<?php echo UCF_Resource_Link_PostType::toHTML( $post ); ?><span class="hidden-xs-up"><?php echo $section_title; ?></span>
									</li>
								<?php endforeach; ?>
								</ul>
							</div>

					<?php
							endif;
						$count++;
						endif;
					endforeach;
					?>
					</div><!-- .row -->
				</div><!-- term/alpha section -->

			<?php endforeach; ?>

			</div><!-- .resource-search -->
		<?php
			return ob_get_clean();
		}

		/**
		 * Outputs the content of the resource.
		 * Use the `ucf_resource_search_display_after` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $resource WP_Post object | The resource
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_resource_search_display_after( $resource ) {
			ob_start();
		?>
			</article>
		<?php
			return ob_get_clean();
		}
	}

	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'enqueue_styles' ) );
	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'enqueue_scripts' ) );
}

?>
