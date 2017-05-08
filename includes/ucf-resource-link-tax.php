<?php
/**
 * Handles the registration of the Resource Link type taxonomy
 * @author RJ Bruneel
 * @since 0.0.1
 **/
if ( ! class_exists( 'UCF_Resource_Link_Type' ) ) {
	class UCF_Resource_Link_Type {
		public static function register_resource_link_type() {
			register_taxonomy( 'resource_link_types', array( 'resource_link' ), self::args() );
			self::register_meta_fields();
		}
		public static function register_meta_fields() {
			add_action( 'resource_link_types_add_form_fields', array( 'UCF_Resource_Link_PostType', 'add_resource_link_types_fields' ), 10, 1 );
			add_action( 'resource_link_types_edit_form_fields', array( 'UCF_Resource_Link_PostType', 'edit_resource_link_types_fields' ), 10, 2 );
			add_action( 'created_resource_link_types', array( 'UCF_Resource_Link_PostType', 'save_resource_link_types_meta' ), 10, 2 );
			add_action( 'edited_resource_link_types', array( 'UCF_Resource_Link_PostType', 'edited_resource_link_types_meta' ), 10, 2 );
		}
		public static function add_resource_link_types_fields( $taxonomy ) {
?>
			<div class="form-field term-group">
				<label for="resource_link_types_alias"><?php _e( 'Resource Link Type Alias', 'ucf_resource_link' ); ?></label>
				<input type="text" id="resource_link_types_alias" name="resource_link_type_alias">
			</div>
			<div class="form-field term-group">
				<label for="resource_link_types_color"><?php _e( 'Resource Link Type Color', 'ucf_resource_link' ); ?></label>
				<input class="wp-color-field" type="text" id="resource_link_types_color" name="resource_link_types_color">
			</div>
<?php
		}
		public static function edit_resource_link_types_fields( $term, $taxonomy ) {
			$alias = get_term_meta( $term->term_id, 'resource_link_types_alias', true );
			$color = get_term_meta( $term->term_id, 'resource_link_types_color', true );
?>
			<tr class="form-field term-group-wrap">
				<th scope="row"><label for="resource_link_types_alias"><?php _e( 'Resource Link Type Alias', 'ucf_resource_link' ); ?></label></th>
				<td><input type="text" id="resource_link_types_alias" name="resource_link_types_alias" value="<?php echo $alias; ?>"></td>
			</tr>
			<tr class="form-field term-group-wrap">
				<th scope="row"><label for="resource_link_types_color"><?php _e( 'Resource Link Type Color', 'ucf_resource_link' ); ?></label></th>
				<td><input class="wp-color-field" type="text" id="resource_link_types_color" name="resource_link_types_color" value="<?php echo $color; ?>"></td>
			</tr>
<?php
		}
		public static function save_resource_link_types_meta( $term_id, $tt_id ) {
			if ( isset( $_POST['resource_link_types_alias'] ) && '' !== $_POST['resource_link_types_alias'] ) {
				$alias = $_POST['resource_link_types_alias'];
				add_term_meta( $term_id, 'resource_link_types_alias', $alias, true );
			}
			if ( isset( $_POST['resource_link_types_color'] ) && '' !== $_POST['resource_link_types_color'] ) {
				$color = $_POST['resource_link_types_color'];
				add_term_meta( $term_id, 'resource_link_types_color', $color, true );
			}
		}
		public static function edited_resource_link_types_meta( $term_id, $tt_id ) {
			if ( isset( $_POST['resource_link_types_alias'] ) && '' !== $_POST['resource_link_types_alias'] ) {
				$alias = $_POST['resource_link_types_alias'];
				update_term_meta( $term_id, 'resource_link_types_alias', $alias, true );
			}
			if ( isset( $_POST['resource_link_types_color'] ) && '' !== $_POST['resource_link_types_color'] ) {
				$color = $_POST['resource_link_types_color'];
				update_term_meta( $term_id, 'resource_link_types_color', $color, true );
			}
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
?>