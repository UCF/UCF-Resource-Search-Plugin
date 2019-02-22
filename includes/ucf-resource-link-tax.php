<?php
/**
 * Handles the registration of the Resource Link type taxonomy
 * @author RJ Bruneel
 * @since 1.0.0
 **/
if ( ! class_exists( 'UCF_Resource_Link_Type' ) ) {
	class UCF_Resource_Link_Type {
		public static function register_resource_link_type() {
			register_taxonomy( 'resource_link_types', array( 'resource_link' ), self::args() );
		}
		public static function labels() {
			$labels = array(
				'singular'  => 'Resource Link Type',
				'plural'    => 'Resource Link Types',
				'post_type' => 'ucf_resource_link'
			);
			return array(
				'name'                       => _x( $labels['plural'], 'Taxonomy General Name', $labels['post_type'] ),
				'singular_name'              => _x( $labels['singular'], 'Taxonomy Singular Name', $labels['post_type'] ),
				'menu_name'                  => __( $labels['singular'], $labels['post_type'] ),
				'all_items'                  => __( 'All ' .$labels['plural'], $labels['post_type'] ),
				'parent_item'                => __( 'Parent ' . $labels['singular'], $labels['post_type'] ),
				'parent_item_colon'          => __( 'Parent ' .$labels['singular'] . ':', $labels['post_type'] ),
				'new_item_name'              => __( 'New ' .$labels['singular'] . ' Name', $labels['post_type'] ),
				'add_new_item'               => __( 'Add New ' . $labels['singular'], $labels['post_type'] ),
				'edit_item'                  => __( 'Edit ' . $labels['singular'], $labels['post_type'] ),
				'update_item'                => __( 'Update ' . $labels['singular'], $labels['post_type'] ),
				'view_item'                  => __( 'View ' . $labels['singular'], $labels['post_type'] ),
				'separate_items_with_commas' => __( 'Separate ' . $labels['singular'] . ' with commas', $labels['post_type'] ),
				'add_or_remove_items'        => __( 'Add or remove ' .$labels['plural'], $labels['post_type'] ),
				'choose_from_most_used'      => __( 'Choose from the most used', $labels['post_type'] ),
				'popular_items'              => __( 'Popular ' .$labels['plural'], $labels['post_type'] ),
				'search_items'               => __( 'Search ' .$labels['plural'], $labels['post_type'] ),
				'not_found'                  => __( 'Not Found', $labels['post_type'] ),
				'no_terms'                   => __( 'No ' .$labels['plural'], $labels['post_type'] ),
				'items_list'                 => __( $labels['singular'] . ' list', $labels['post_type'] ),
				'items_list_navigation'      => __( $labels['singular'] . ' list navigation', $labels['post_type'] ),
			);
		}
		public static function args() {
			return array(
				'labels'                     => self::labels(),
				'hierarchical'               => true,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
			);
		}
	}
}

/**
 * Handles the registration of the Resource Link category taxonomy
 * @author RJ Bruneel
 * @since 1.0.4
 **/
if ( ! class_exists( 'UCF_Resource_Link_Category' ) ) {
	class UCF_Resource_Link_Category {
		public static function register_resource_link_category() {
			register_taxonomy( 'resource_link_category', array( 'resource_link' ), self::args() );
		}
		public static function labels() {
			$labels = array(
				'singular'  => 'Resource Link Category',
				'plural'    => 'Resource Link Categories',
				'post_type' => 'ucf_resource_link'
			);
			return array(
				'name'                       => _x( $labels['plural'], 'Taxonomy General Name', $labels['post_type'] ),
				'singular_name'              => _x( $labels['singular'], 'Taxonomy Singular Name', $labels['post_type'] ),
				'menu_name'                  => __( $labels['singular'], $labels['post_type'] ),
				'all_items'                  => __( 'All ' .$labels['plural'], $labels['post_type'] ),
				'parent_item'                => __( 'Parent ' . $labels['singular'], $labels['post_type'] ),
				'parent_item_colon'          => __( 'Parent ' .$labels['singular'] . ':', $labels['post_type'] ),
				'new_item_name'              => __( 'New ' .$labels['singular'] . ' Name', $labels['post_type'] ),
				'add_new_item'               => __( 'Add New ' . $labels['singular'], $labels['post_type'] ),
				'edit_item'                  => __( 'Edit ' . $labels['singular'], $labels['post_type'] ),
				'update_item'                => __( 'Update ' . $labels['singular'], $labels['post_type'] ),
				'view_item'                  => __( 'View ' . $labels['singular'], $labels['post_type'] ),
				'separate_items_with_commas' => __( 'Separate ' . $labels['singular'] . ' with commas', $labels['post_type'] ),
				'add_or_remove_items'        => __( 'Add or remove ' .$labels['plural'], $labels['post_type'] ),
				'choose_from_most_used'      => __( 'Choose from the most used', $labels['post_type'] ),
				'popular_items'              => __( 'Popular ' .$labels['plural'], $labels['post_type'] ),
				'search_items'               => __( 'Search ' .$labels['plural'], $labels['post_type'] ),
				'not_found'                  => __( 'Not Found', $labels['post_type'] ),
				'no_terms'                   => __( 'No ' .$labels['plural'], $labels['post_type'] ),
				'items_list'                 => __( $labels['singular'] . ' list', $labels['post_type'] ),
				'items_list_navigation'      => __( $labels['singular'] . ' list navigation', $labels['post_type'] ),
			);
		}
		public static function args() {
			return array(
				'labels'                     => self::labels(),
				'hierarchical'               => true,
				'public'                     => true,
				'show_ui'                    => true,
				'show_admin_column'          => true,
				'show_in_nav_menus'          => true,
				'show_tagcloud'              => true,
			);
		}
	}
}
?>
