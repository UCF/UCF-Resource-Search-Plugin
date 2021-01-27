<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF_Resource_Search_Common' ) ) {
	class UCF_Resource_Search_Common {

		public static function enqueue_styles() {
			if ( filter_var( get_option( 'ucf_resource_search_include_css', true ), FILTER_VALIDATE_BOOLEAN ) ) {
				wp_enqueue_style( 'ucf_resource_search_css', UCF_RESOURCE_SEARCH__STYLES_URL . '/ucf-resource-search.min.css', false, false, 'all' );
			}
		}

		public static function enqueue_scripts() {
			wp_enqueue_script( 'ucf-resource_search_js', UCF_RESOURCE_SEARCH__SCRIPT_URL . '/ucf-resource-search.min.js', null, null, true );
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

			$args['show_empty_sections'] = filter_var( $args['show_empty_sections'], FILTER_VALIDATE_BOOLEAN );
			$args['column_count']        = is_numeric( $args['column_count'] ) ? (int)$args['column_count'] : $defaults['column_count'];
			$args['show_sorting']        = filter_var( $args['show_sorting'], FILTER_VALIDATE_BOOLEAN );

			if ( !in_array( $args['default_sorting'], array( 'term', 'alpha' ) ) ) {
				$args['default_sorting'] = $default['default_sorting'];
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

	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'enqueue_styles' ) );
	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'enqueue_scripts' ) );
}

?>
