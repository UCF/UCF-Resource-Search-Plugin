<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF_Resource_Search_Common' ) ) {
	class UCF_Resource_Search_Common {

		public static function register_assets() {
			$plugin_data = get_plugin_data( UCF_RESOURCE_SEARCH__PLUGIN_FILE, false, false );
			$version     = $plugin_data['Version'];

			if ( filter_var( get_option( 'ucf_resource_search_include_css' ), FILTER_VALIDATE_BOOLEAN ) ) {
				wp_register_style( 'ucf_resource_search_css', UCF_RESOURCE_SEARCH__STYLES_URL . '/ucf-resource-search.min.css', null, $version, 'all' );
			}
			wp_register_script( 'ucf-resource_search_js', UCF_RESOURCE_SEARCH__SCRIPT_URL . '/ucf-resource-search.min.js', array( 'jquery' ), $version, true );
		}

		public static function enqueue_styles() {
			if ( wp_style_is( 'ucf_resource_search_css', 'registered' ) ) {
				wp_enqueue_style( 'ucf_resource_search_css' );
			}
		}

		public static function enqueue_scripts( $args=null ) {
			wp_enqueue_script( 'ucf-resource_search_js' );

			if ( $args ) {
				// Register the search data with the JS PostTypeSearchDataManager.
				// Format is array(post->ID=>terms) where terms include the post title
				// as well as all associated tag names
				$items = array();
				foreach ( get_posts( array( 'numberposts' => -1, 'post_type' => $args['post_type_name'] ) ) as $post ) {
					$items[$post->ID] = array( $post->post_title );
					foreach ( wp_get_object_terms( $post->ID, 'post_tag' ) as $term ) {
						$items[$post->ID][] = $term->name;
					}
				}

				ob_start();
			?>
				(function($) {
					PostTypeSearchDataManager.register(new PostTypeSearchData(
						<?php echo json_encode( $args['column_count'] ); ?>,
						<?php echo json_encode( $args['column_width'] ); ?>,
						<?php echo json_encode( $items ); ?>
					));
				})(jQuery);
			<?php
				$inline_script = trim( ob_get_clean() );
				wp_add_inline_script( 'ucf-resource_search_js', $inline_script );
			}
		}


		/**
		 * Displays the output of the resource search content.
		 *
		 * @author R.J. Bruneel
		 * @since 1.0.0
		 *
		 * @param $args Array | An array of attributes.
		 *
		 * @return string | The output of the resource search content.
		 **/
		public static function display_resource_search( $args ) {
			self::enqueue_scripts( $args );

			$args['show_empty_sections'] = filter_var( $args['show_empty_sections'], FILTER_VALIDATE_BOOLEAN );
			$args['column_count']        = is_numeric( $args['column_count'] ) ? (int)$args['column_count'] : 3;
			$args['show_sorting']        = filter_var( $args['show_sorting'], FILTER_VALIDATE_BOOLEAN );

			if ( !in_array( $args['default_sorting'], array( 'term', 'alpha' ) ) ) {
				$args['default_sorting'] = 'term';
			}

			if ( has_filter( 'ucf_resource_link_display_' . $args['layout'] . '_before' ) ) {
				$before = apply_filters( 'ucf_resource_link_display_' . $args['layout'] . '_before', '', $args );
			}

			if ( has_filter( 'ucf_resource_link_display_' . $args['layout'] ) ) {
				$content = apply_filters( 'ucf_resource_link_display_' . $args['layout'], '', $args );
			}

			if ( has_filter( 'ucf_resource_link_display_' . $args['layout'] . '_after' ) ) {
				$after = apply_filters( 'ucf_resource_link_display_' . $args['layout'] . '_after', '' , $args );
			}

			return $before . $content . $after;
		}


		/**
		 * Displays the output of the filter nav.
		 *
		 * @author R.J. Bruneel
		 * @since 1.0.4
		 *
		 * @param $nav_class string | CSS class to display the filter nav horizontally, vertically or other Athena nav classes.
		 * @param $label string | Content to display as the label for the filters.
		 *
		 * @return string | The filter nav HTML.
		 **/
		public static function display_filter_nav( $nav_class, $label ) {
			$terms = get_terms( array(
				'taxonomy'   => 'resource_link_category',
				'hide_empty' => true,
			) );

			if ( $terms && !is_wp_error( $terms ) ) :
				ob_start();
		?>
				<ul class="ucf-resource-list-filter <?php echo $nav_class; ?> mb-4">
					<?php echo $label; ?>
					<li class="nav-item"><a href="#filter-all" class="filter-all pb-1 pb-md-2 nav-link active text-secondary">Show All</a></li>
					<?php foreach ( $terms as $term ) { ?>
						<li class="nav-item"><a href="#filter-<?php echo $term->slug; ?>" class="filter-<?php echo $term->slug; ?> nav-link pb-1 pb-md-2"><?php echo $term->name; ?></a></li>
					<?php } ?>
				</ul>
		<?php
				return ob_get_clean();
			else:
				return '';
			endif;
		}

	}

	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'register_assets' ), 10, 0 );
	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'enqueue_styles' ), 11, 0 );
}

?>
