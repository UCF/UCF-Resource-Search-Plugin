<?php
/**
 * Registers the A-Z Index shortcode
 * @author RJ Bruneel
 * @since 1.0.0
 **/

if ( ! class_exists( 'UCF_Index_Shortcode' ) ) {
	class UCF_Index_Shortcode {
		public static function shortcode( $atts ) {
			$atts = shortcode_atts( array(
				'slug'  => null,
				'id'    => null
			), $atts );

			if ( isset( $atts['slug'] ) || isset( $atts['id'] ) ) {
				return UCF_Index_Common::display_index( $atts );
			}

			return '';
		}
	}
	add_shortcode( 'ucf-index', array( 'UCF_Index_Shortcode', 'shortcode' ) );
}
?>
