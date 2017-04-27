<?php
/**
 * Place common functions here.
 **/

if ( ! class_exists( 'UCF__Index_Common' ) ) {
	class UCF_Index_Common {

		/**
		 * Displays the output of the index.
		 *
		 * @author R.J. Bruneel
		 * @since 1.0.0
		 *
		 * @param $attr Array | An array of attributes.
		 *
		 * @return string | The output of the index content.
		 **/
		public static function display_index( $attr ) {

			$before = self::ucf_index_display_before( $index );
			if ( has_filter( 'ucf_index_display_before' ) ) {
				$before = apply_filters( 'ucf_index_display_before', $output, $index );
			}

			$content = self::ucf_index_display( $index );
			if ( has_filter( 'ucf_index_display' ) ) {
				$content = apply_filters( 'ucf_index_display', $output, $index );
			}

			$after = self::ucf_index_display_after( $index );
			if ( has_filter( 'ucf_index_display_after' ) ) {
				$after += apply_filters( 'ucf_index_display_after', $output, $index );
			}

			$retval = $before . $content . $after;

			return $retval;
		}

		/**
		 * Prepends the index content with a index tag.
		 * Use the `ucf_index_display_before` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $index WP_Post object | The index
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_index_display_before( $index ) {
			ob_start();
		?>
			<article>
		<?php
			return ob_get_clean();
		}

		/**
		 * Outputs the content of the index.
		 * Use the `ucf_index_display` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $index WP_Post object | The index
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_index_display( $index ) {
			ob_start();
		?>
			A-Z Index Goes Here
		<?php
			return ob_get_clean();
		}

		/**
		 * Outputs the content of the index.
		 * Use the `ucf_index_display_after` filter
		 * hook to override or modify this output.
		 *
		 * @author Jim Barnes
		 * @since 1.0.0
		 *
		 * @param $index WP_Post object | The index
		 *
		 * @return string | The html to be appended to output.
		 **/
		public static function ucf_index_display_after( $index ) {
			ob_start();
		?>
			</article>
		<?php
			return ob_get_clean();
		}
	}
}

?>
