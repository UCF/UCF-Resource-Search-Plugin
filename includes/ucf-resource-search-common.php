<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF_Resource_Search_Common' ) ) {
	class UCF_Resource_Search_Common {

		public function enqueue_styles() {
			if ( get_option( 'ucf_resource_search_include_css' ) ) {
				wp_enqueue_style( 'ucf_resource_search_css', plugins_url( 'static/css/ucf-resource-search.min.css', UCF_Resource_Search__PLUGIN_FILE ), false, false, 'all' );
			}
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

			$before = self::ucf_resource_search_display_before( $resource );
			if ( has_filter( 'ucf_resource_search_display_before' ) ) {
				$before = apply_filters( 'ucf_resource_search_display_before', $output, $resource );
			}

			$content = self::ucf_resource_search_display( $params );
			if ( has_filter( 'ucf_resource_search_display' ) ) {
				$content = apply_filters( 'ucf_resource_search_display', $output, $params );
			}

			$after = self::ucf_resource_search_display_after( $resource );
			if ( has_filter( 'ucf_resource_search_display_after' ) ) {
				$after += apply_filters( 'ucf_resource_search_display_after', $output, $resource );
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
		public static function ucf_resource_search_display( $params ) {
			ob_start();
		?>
			<script type="text/javascript">
				if(typeof PostTypeSearchDataManager != 'undefined') {
					PostTypeSearchDataManager.register(new PostTypeSearchData(
						<?php echo json_encode( $params['column_count'] ); ?>,
						<?php echo json_encode( $params['column_width'] ); ?>,
						<?php echo json_encode( $search_data ); ?>
					));
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
				'post-type-search-term'  => $by_term,
				'post-type-search-alpha' => $by_alpha,
			);
			ob_start();
		?>
			<div class="resource-search">
				<div class="resource-search-header">
					<form class="resource-search-form form-inline" action="." method="get">
						<label class="resource-search-label">
							<h2 class="h4 font-sans-serif text-uppercase">
								<?php echo $params['default_search_label']; ?>
							</h2>
							<input type="text" class="form-control resource-search-input" placeholder="<?php echo $params['default_search_text']; ?>">
						</label>
					</form>
				</div>
				<div class="resource-search-results"></div>
				<?php if ( $params['show_sorting'] ) { ?>
				<div class="btn-group resource-search-sorting">
					<button class="btn btn-default<?php if ( $params['default_sorting'] == 'term' ) echo ' active'; ?>">
						<span class="glyphicon glyphicon-list-alt"></span>
					</button>
					<button class="btn btn-default<?php if ( $params['default_sorting'] == 'alpha' ) echo ' active'; ?>">
						<span class="glyphicon glyphicon-font"></span>
					</button>
				</div>
				<?php } ?>

				<div class="btn-toolbar jump-to-list" role="toolbar" aria-label="Jump To List">
					<div class="btn-group" role="group">
						<a href="#az-a" class="btn btn-default">A</a>
						<a href="#az-b" class="btn btn-default">B</a>
						<a href="#az-c" class="btn btn-default">C</a>
						<a href="#az-d" class="btn btn-default">D</a>
						<a href="#az-e" class="btn btn-default">E</a>
						<a href="#az-f" class="btn btn-default">F</a>
						<a href="#az-g" class="btn btn-default">G</a>
						<a href="#az-h" class="btn btn-default">H</a>
						<a href="#az-i" class="btn btn-default">I</a>
						<a href="#az-j" class="btn btn-default">J</a>
						<a href="#az-k" class="btn btn-default">K</a>
						<a href="#az-l" class="btn btn-default">L</a>
						<a href="#az-m" class="btn btn-default">M</a>
						<br class="visible-xs visible-sm">
						<a href="#az-n" class="btn btn-default">N</a>
						<a href="#az-o" class="btn btn-default">O</a>
						<a href="#az-p" class="btn btn-default">P</a>
						<a href="#az-q" class="btn btn-default disabled">Q</a>
						<a href="#az-r" class="btn btn-default">R</a>
						<a href="#az-s" class="btn btn-default">S</a>
						<a href="#az-t" class="btn btn-default">T</a>
						<a href="#az-u" class="btn btn-default">U</a>
						<a href="#az-v" class="btn btn-default">V</a>
						<a href="#az-w" class="btn btn-default">W</a>
						<a href="#az-x" class="btn btn-default disabled">X</a>
						<a href="#az-y" class="btn btn-default disabled">Y</a>
						<a href="#az-z" class="btn btn-default disabled">Z</a>
					</div>
				</div>

			<?php
			foreach ( $sections as $id => $section ):
				$hide = false;
				switch ( $id ) {
					case 'post-type-search-alpha':
						if ( $params['default_sorting'] == 'term' ) {
							$hide = True;
						}
						break;
					case 'post-type-search-term':
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
										<div class="resource-search-heading-wrap">
											<h3 class="resource-search-heading font-slab-serif"><?php echo esc_html( $section_title ); ?></h3>
											<hr class="hr-3 hr-primary">
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
												<?php echo UCF_Resource_Link_PostType::toHTML( $post ); ?><span class="hidden-xs-up"><?php echo $section_title; ?></span>
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
								<div class="resource-search-heading-wrap">
									<h3 class="resource-search-heading font-slab-serif"><?php echo esc_html( $section_title ); ?></h3>
									<span class="to-top-text text-uppercase"><span class="fa fa-long-arrow-up"></span> <a href="#">Back to Top</a></span>
									<hr class="hr-3 hr-primary">
								</div>
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

			</div><!-- .post-type-search -->
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
}

?>
