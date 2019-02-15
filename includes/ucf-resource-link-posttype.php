<?php
/**
 * Handles the registration of the Resource Link custom post type.
 * @author RJ Bruneel
 * @since 1.0.0
 **/
if ( ! class_exists( 'UCF_Resource_Link_PostType' ) ) {
	class UCF_Resource_Link_PostType {
		/**
		 * Registers the custom post type.
		 * @author RJ Bruneel
		 * @since 1.0.0
		 **/
		public static function register() {
			$labels = self::get_labels();
			register_post_type( 'ucf_resource_link', self::args( $labels ) );
			add_action( 'add_meta_boxes', array( 'UCF_Resource_Link_PostType', 'register_metabox' ) );
			add_action( 'save_post', array( 'UCF_Resource_Link_PostType', 'save_metabox' ) );
		}

		public static function get_labels() {
			return apply_filters(
				'ucf_resource_link_labels',
				array(
					'singular'  => 'Resource Link',
					'plural'    => 'Resource Links',
					'post_type' => 'ucf_resource_link'
				)
			);
		}

		/**
		* Outputs this item in HTML.
		 * @author RJ Bruneel
		 * @since 1.0.0
		**/
		public static function toHTML($object){
			$html = '<a class="resource-link" href="'.get_post_meta($object->ID, 'ucf_resource_link_url', TRUE).'">'.$object->post_title.'</a>';
			return $html;
		}

		/**
		 * Adds a metabox to the Resource link custom post type.
		 * @author RJ Bruneel
		 * @since 1.0.0
		 **/
		public static function register_metabox() {
			add_meta_box(
				'ucf_resource_link_metabox',
				'Resource Link Details',
				array( 'UCF_Resource_Link_PostType', 'register_metafields' ),
				'ucf_resource_link',
				'normal',
				'high'
			);
		}
		/**
		 * Adds metafields to the metabox
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $post WP_POST object
		 **/
		public static function register_metafields( $post ) {
			wp_nonce_field( 'ucf_resource_link_nonce_save', 'ucf_resource_link_nonce' );
			$url = get_post_meta( $post->ID, 'ucf_resource_link_url', TRUE );
			$admins = get_post_meta( $post->ID, 'ucf_resource_link_admins', TRUE );
			$facebook = get_post_meta( $post->ID, 'ucf_resource_facebook_url', TRUE );
			$twitter = get_post_meta( $post->ID, 'ucf_resource_twitter_url', TRUE );
			$instagram = get_post_meta( $post->ID, 'ucf_resource_instagram_url', TRUE );
			$linkedin = get_post_meta( $post->ID, 'ucf_resource_linkedin_url', TRUE );
			$youtube = get_post_meta( $post->ID, 'ucf_resource_youtube_url', TRUE );
?>
			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<label class="block" for="ucf_resource_link_url"><strong>Website URL</strong></label>
						</th>
						<td>
							<input type="text" id="ucf_resource_link_url" name="ucf_resource_link_url" class="regular-text" <?php echo ( ! empty( $url ) ) ? 'value="' . $url . '"' : ''; ?>>
						</td>
					</tr>
					<tr>
						<th>
							<label class="block" for="ucf_resource_facebook_url"><strong>Facebook URL</strong></label>
						</th>
						<td>
							<input type="text" id="ucf_resource_facebook_url" name="ucf_resource_facebook_url" class="regular-text" <?php echo ( ! empty( $facebook ) ) ? 'value="' . $facebook . '"' : ''; ?>>
							<p class="description">The resource Facebook page URL. https://www.facebook.com/ResourceName/</p>
						</td>
					</tr>
					<tr>
						<th>
							<label class="block" for="ucf_resource_twitter_url"><strong>Twitter URL</strong></label>
						</th>
						<td>
							<input type="text" id="ucf_resource_twitter_url" name="ucf_resource_twitter_url" class="regular-text" <?php echo ( ! empty( $twitter ) ) ? 'value="' . $twitter . '"' : ''; ?>>
							<p class="description">The resource Twitter page URL. https://www.twitter.com/ResourceName/</p>
						</td>
					</tr>
					<tr>
						<th>
							<label class="block" for="ucf_resource_instagram_url"><strong>Instagram URL</strong></label>
						</th>
						<td>
							<input type="text" id="ucf_resource_instagram_url" name="ucf_resource_instagram_url" class="regular-text" <?php echo ( ! empty( $instagram ) ) ? 'value="' . $instagram . '"' : ''; ?>>
							<p class="description">The resource Instagram page URL. https://www.instagram.com/ResourceName/</p>
						</td>
					</tr>
					<tr>
						<th>
							<label class="block" for="ucf_resource_linkedin_url"><strong>LinkedIn URL</strong></label>
						</th>
						<td>
							<input type="text" id="ucf_resource_linkedin_url" name="ucf_resource_linkedin_url" class="regular-text" <?php echo ( ! empty( $linkedin ) ) ? 'value="' . $linkedin . '"' : ''; ?>>
							<p class="description">The resource LinkedIn page URL. https://www.linkedin.com/in/ResourceName/</p>
						</td>
					</tr>
					<tr>
						<th>
							<label class="block" for="ucf_resource_youtube_url"><strong>YouTube URL</strong></label>
						</th>
						<td>
							<input type="text" id="ucf_resource_youtube_url" name="ucf_resource_youtube_url" class="regular-text" <?php echo ( ! empty( $youtube ) ) ? 'value="' . $youtube . '"' : ''; ?>>
							<p class="description">The resource YouTube page URL. https://www.youtube.com/ResourceName</p>
						</td>
					</tr>
					<tr>
						<th>
							<label class="block" for="ucf_resource_link_admins"><strong>Web Administrators</strong></label>
						</th>
						<td>
							<textarea id="ucf_resource_link_admins" name="ucf_resource_link_admins" class="regular-text"><?php echo ( ! empty( $admins ) ) ? $admins : ''; ?></textarea>
							<p class="description">Add web administrator information here. Accepts HTML content.</p>
						</td>
					</tr>
				</tbody>
			</table>
<?php
		}
		/**
		 * Handles saving the data in the metabox
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $post_id WP_POST post id
		 **/
		public static function save_metabox( $post_id ) {
			$post_type = get_post_type( $post_id );
			// If this isn't a resource link, return.
			if ( 'ucf_resource_link' !== $post_type ) return;
			if ( isset( $_POST['ucf_resource_link_url'] ) ) {
				// Ensure field is valid.
				$url = sanitize_text_field( $_POST['ucf_resource_link_url'] );
				if ( $url ) {
					update_post_meta( $post_id, 'ucf_resource_link_url', $url );
				}
			}
			if ( isset( $_POST['ucf_resource_facebook_url'] ) ) {
				// Ensure field is valid.
				$url = sanitize_text_field( $_POST['ucf_resource_facebook_url'] );
				if ( $url ) {
					update_post_meta( $post_id, 'ucf_resource_facebook_url', $url );
				}
			}
			if ( isset( $_POST['ucf_resource_twitter_url'] ) ) {
				// Ensure field is valid.
				$url = sanitize_text_field( $_POST['ucf_resource_twitter_url'] );
				if ( $url ) {
					update_post_meta( $post_id, 'ucf_resource_twitter_url', $url );
				}
			}
			if ( isset( $_POST['ucf_resource_instagram_url'] ) ) {
				// Ensure field is valid.
				$url = sanitize_text_field( $_POST['ucf_resource_instagram_url'] );
				if ( $url ) {
					update_post_meta( $post_id, 'ucf_resource_instagram_url', $url );
				}
			}
			if ( isset( $_POST['ucf_resource_linkedin_url'] ) ) {
				// Ensure field is valid.
				$url = sanitize_text_field( $_POST['ucf_resource_linkedin_url'] );
				if ( $url ) {
					update_post_meta( $post_id, 'ucf_resource_linkedin_url', $url );
				}
			}
			if ( isset( $_POST['ucf_resource_youtube_url'] ) ) {
				// Ensure field is valid.
				$url = sanitize_text_field( $_POST['ucf_resource_youtube_url'] );
				if ( $url ) {
					update_post_meta( $post_id, 'ucf_resource_youtube_url', $url );
				}
			}
		}
		/**
		 * Returns an array of labels for the custom post type.
		 * @author RJ Bruneel
		 * @since 1.0.0
		 * @param $singular string | The singular form for the CPT labels.
		 * @param $plural string | The plural form for the CPT labels.
		 * @param $post_type string | The post type name.
		 * @return Array
		 **/
		public static function labels( $singular, $plural, $post_type ) {
			return array(
				'name'                  => _x( $plural, 'Post Type General Name', $post_type ),
				'singular_name'         => _x( $singular, 'Post Type Singular Name', $post_type ),
				'menu_name'             => __( $plural, $post_type ),
				'name_admin_bar'        => __( $singular, $post_type ),
				'archives'              => __( $plural . ' Archives', $post_type ),
				'parent_item_colon'     => __( 'Parent ' . $singular . ':', $post_type ),
				'all_items'             => __( 'All ' . $plural, $post_type ),
				'add_new_item'          => __( 'Add New ' . $singular, $post_type ),
				'add_new'               => __( 'Add New', $post_type ),
				'new_item'              => __( 'New ' . $singular, $post_type ),
				'edit_item'             => __( 'Edit ' . $singular, $post_type ),
				'update_item'           => __( 'Update ' . $singular, $post_type ),
				'view_item'             => __( 'View ' . $singular, $post_type ),
				'search_items'          => __( 'Search ' . $plural, $post_type ),
				'not_found'             => __( 'Not found', $post_type ),
				'not_found_in_trash'    => __( 'Not found in Trash', $post_type ),
				'featured_image'        => __( 'Featured Image', $post_type ),
				'set_featured_image'    => __( 'Set featured image', $post_type ),
				'remove_featured_image' => __( 'Remove featured image', $post_type ),
				'use_featured_image'    => __( 'Use as featured image', $post_type ),
				'insert_into_item'      => __( 'Insert into ' . $singular, $post_type ),
				'uploaded_to_this_item' => __( 'Uploaded to this ' . $singular, $post_type ),
				'items_list'            => __( $plural . ' list', $post_type ),
				'items_list_navigation' => __( $plural . ' list navigation', $post_type ),
				'filter_items_list'     => __( 'Filter ' . $plural . ' list', $post_type ),
			);
		}
		public static function args( $labels ) {
			$args = array(
				'label'                 => __( $labels['singular'], 'ucf_resource_link' ),
				'description'           => __( $labels['plural'], 'ucf_resource_link' ),
				'labels'                => self::labels( $labels['singular'], $labels['plural'], $labels['post_type'] ),
				'supports'              => array( 'title', 'revisions'),
				'taxonomies'            => self::taxonomies(),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'menu_icon'             => 'dashicons-admin-links',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'post',
			);
			$args = apply_filters( 'ucf_resource_link_post_type_args', $args );
			return $args;
		}
		public static function taxonomies() {
			$retval = array(
				'resource_link_types',
				'resource_link_category'
			);
			$retval = apply_filters( 'resource_link_taxonomies', $retval );
			foreach( $retval as $taxonomy ) {
				if ( ! taxonomy_exists( $taxonomy ) ) {
					unset( $retval[$taxonomy] );
				}
			}
			return $retval;
		}
	}
	add_action( 'init', array( 'UCF_Resource_Link_Type', 'register_resource_link_type' ), 10, 0 );
	add_action( 'init', array( 'UCF_Resource_Link_Category', 'register_resource_link_category' ), 10, 0 );
	add_action( 'init', array( 'UCF_Resource_Link_PostType', 'register' ), 10, 0 );
}
?>
