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

	}

	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'enqueue_styles' ) );
	add_action( 'wp_enqueue_scripts', array( 'UCF_Resource_Search_Common', 'enqueue_scripts' ) );
}

?>
