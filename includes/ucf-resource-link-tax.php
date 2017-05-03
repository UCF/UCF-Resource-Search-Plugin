<?php
/**
 * Handles the registration of the Resource Link type taxonomy
 * @author RJ Bruneel
 * @since 0.0.1
 **/
if ( ! class_exists( 'UCF_Resource_Link_Type' ) ) {
	class UCF_Resource_Link_Type {
		public static function register_resource_link_type() {
			register_taxonomy( 'resource_link_types', array( 'degree' ), self::args() );
			self::register_meta_fields();
		}
		public static function register_meta_fields() {
			add_action( 'resource_link_types_add_form_fields', array( 'Resource_Link_Type', 'add_resource_link_types_fields' ), 10, 1 );
			add_action( 'resource_link_types_edit_form_fields', array( 'Resource_Link_Type', 'edit_resource_link_types_fields' ), 10, 2 );
			add_action( 'created_resource_link_types', array( 'Resource_Link_Type', 'save_resource_link_types_meta' ), 10, 2 );
			add_action( 'edited_resource_link_types', array( 'Resource_Link_Type', 'edited_resource_link_types_meta' ), 10, 2 );
		}
		public static function add_resource_link_types_fields( $taxonomy ) {
?>
			<div class="form-field term-group">
				<label for="resource_link_types_alias"><?php _e( 'Resource Link Type Alias', 'ucf_degree' ); ?></label>
				<input type="text" id="resource_link_types_alias" name="resource_link_type_alias">
			</div>
			<div class="form-field term-group">
				<label for="resource_link_types_color"><?php _e( 'Resource Link Type Color', 'ucf_degree' ); ?></label>
				<input class="wp-color-field" type="text" id="resource_link_types_color" name="resource_link_types_color">
			</div>
<?php
		}
		public static function edit_resource_link_types_fields( $term, $taxonomy ) {
			$alias = get_term_meta( $term->term_id, 'resource_link_types_alias', true );
			$color = get_term_meta( $term->term_id, 'resource_link_types_color', true );
?>
			<tr class="form-field term-group-wrap">
				<th scope="row"><label for="resource_link_types_alias"><?php _e( 'Resource Link Type Alias', 'ucf_degree' ); ?></label></th>
				<td><input type="text" id="resource_link_types_alias" name="resource_link_types_alias" value="<?php echo $alias; ?>"></td>
			</tr>
			<tr class="form-field term-group-wrap">
				<th scope="row"><label for="resource_link_types_color"><?php _e( 'Resource Link Type Color', 'ucf_degree' ); ?></label></th>
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
			return array(
				'name'                       => _x( 'Resource Link Types', 'Taxonomy General Name', 'ucf_degree' ),
				'singular_name'              => _x( 'Resource Link Type', 'Taxonomy Singular Name', 'ucf_degree' ),
				'menu_name'                  => __( 'Resource Link Types', 'ucf_degree' ),
				'all_items'                  => __( 'All Resource Link Types', 'ucf_degree' ),
				'parent_item'                => __( 'Parent Resource Link Type', 'ucf_degree' ),
				'parent_item_colon'          => __( 'Parent Resource Link Type:', 'ucf_degree' ),
				'new_item_name'              => __( 'New Resource Link Type Name', 'ucf_degree' ),
				'add_new_item'               => __( 'Add New Resource Link Type', 'ucf_degree' ),
				'edit_item'                  => __( 'Edit Resource Link Type', 'ucf_degree' ),
				'update_item'                => __( 'Update Resource Link Type', 'ucf_degree' ),
				'view_item'                  => __( 'View Resource Link Type', 'ucf_degree' ),
				'separate_items_with_commas' => __( 'Separate Resource Link types with commas', 'ucf_degree' ),
				'add_or_remove_items'        => __( 'Add or remove Resource Link types', 'ucf_degree' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'ucf_degree' ),
				'popular_items'              => __( 'Popular Resource Link Types', 'ucf_degree' ),
				'search_items'               => __( 'Search Resource Link Types', 'ucf_degree' ),
				'not_found'                  => __( 'Not Found', 'ucf_degree' ),
				'no_terms'                   => __( 'No Resource Link types', 'ucf_degree' ),
				'items_list'                 => __( 'Resource Link Types list', 'ucf_degree' ),
				'items_list_navigation'      => __( 'Resource Link types list navigation', 'ucf_degree' ),
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