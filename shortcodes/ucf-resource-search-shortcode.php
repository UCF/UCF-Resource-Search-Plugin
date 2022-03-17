<?php
/**
 * Registers the Resource Search shortcode
 * @author RJ Bruneel
 * @since 1.0.0
 **/

if ( ! class_exists( 'UCF_Resource_Search_Shortcode' ) ) {
	class UCF_Resource_Search_Shortcode {
		public static function resource_search_shortcode( $args ) {
			$defaults = array(
				'post_type_name'            => 'post',
				'taxonomy'                  => 'category',
				'meta_key'                  => '',
				'meta_value'                => '',
				'show_empty_sections'       => false,
				'non_alpha_section_name'    => 'Other',
				'column_width'              => 'col-md-4 col-sm-4',
				'column_count'              => '3',
				'order_by'                  => 'title',
				'order'                     => 'ASC',
				'show_sorting'              => true,
				'default_sorting'           => 'term',
				'show_uncategorized'        => false,
				'uncategorized_term_name'   => 'Uncategorized',
				'layout'                    => 'classic',
				'nav_position'              => 'top',
				'resource_type_filter'      => '',
				'card_layout_title_element' => 'div'
			);

			$args = ( $args === '' ) ? $defaults : array_merge( $defaults, $args );

			ob_start();
			echo UCF_Resource_Search_Common::display_resource_search( $args );
			return ob_get_clean();
		}
	}
	add_shortcode( 'ucf-resource-search', array( 'UCF_Resource_Search_Shortcode', 'resource_search_shortcode' ) );
}
?>
