<?php

/**
 * Prepends the resource search content with a resource_search tag.
 * Use the `ucf_resource_link_display_classic_before` filter
 * hook to override or modify this output.
 *
 * @author RJ Bruneel
 * @since 1.0.4
 *
 * @param $content WP_Post object | The content
 * @param $args Array | Array of arguments
 *
 * @return string | The html to be appended to output.
 **/
if ( ! function_exists( 'ucf_resource_link_display_classic_before' ) ) {
	function ucf_resource_link_display_classic_before( $content, $args ) {
		ob_start();
	?>
		<article>
	<?php
		return ob_get_clean();
	}
}
add_filter( 'ucf_resource_link_display_classic_before', 'ucf_resource_link_display_classic_before', 10, 2 );

/**
 * Outputs the content of the resource.
 * Use the `ucf_resource_link_display_classic` filter
 * hook to override or modify this output.
 *
 * @author RJ Bruneel
 * @since 1.0.4
 *
 * @param $content WP_Post object | The content
 * @param $args Array | Array of arguments
 *
 * @return string | The html to be appended to output.
 **/
if ( ! function_exists( 'ucf_resource_link_display_classic' ) ) {
	function ucf_resource_link_display_classic( $content, $args ) {

		$labels = UCF_Resource_Link_PostType::get_labels();

		// Set default search text if the user didn't
		if ( !isset( $args['default_search_text'] ) ) {
			$args['default_search_text'] = 'Find a ' . $labels['singular'];
		}

		// Set default search label if the user didn't
		if ( !isset( $args['default_search_label'] ) ) {
			$args['default_search_label'] = 'Find a ' . $labels['singular'];
		}

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
		<script type="text/javascript">
			if (typeof jQuery !== 'undefined') {
				jQuery(document).ready(function ($) {
					PostTypeSearchDataManager.register(new PostTypeSearchData(
						<?php echo json_encode( $args['column_count'] ); ?>,
						<?php echo json_encode( $args['column_width'] ); ?>,
						<?php echo json_encode( $items ); ?>
					));
				});
			} else {
				console.log('jQuery dependency failed to load');
			}
		</script>
		<?php
		$script = ob_get_clean();

		// Set up a post query
		$query_args = array(
			'numberposts' => -1,
			'post_type'   => $args['post_type_name'],
			'tax_query'   => array(
				array(
					'taxonomy' => $args['taxonomy'],
					'field'    => 'id',
					'terms'    => '',
				)
			),
			'orderby'     => $args['order_by'],
			'order'       => $args['order'],
		);

		// Handle meta key and value query
		if ( $args['meta_key'] && $args['meta_value'] ) {
			$query_args['meta_key'] = $args['meta_key'];
			$query_args['meta_value'] = $args['meta_value'];
		}
		// Split up this post type's posts by term
		$by_term = array();
		foreach ( get_terms( $args['taxonomy'] ) as $term ) { // get_terms defaults to an orderby=name, order=asc value
			$query_args['tax_query'][0]['terms'] = $term->term_id;
			$posts = get_posts( $query_args );
			if ( count( $posts ) == 0 && $args['show_empty_sections'] ) {
				$by_term[$term->name] = array();
			} else {
				$by_term[$term->name] = $posts;
			}
		}
		// Add uncategorized items to posts by term if parameter is set.
		if ( $args['show_uncategorized'] ) {
			$terms = get_terms( $args['taxonomy'], array( 'fields' => 'ids', 'hide_empty' => false ) );
			$query_args['tax_query'][0]['terms'] = $terms;
			$query_args['tax_query'][0]['operator'] = 'NOT IN';
			$uncat_posts = get_posts( $query_args );
			if ( count( $uncat_posts == 0 ) && $args['show_empty_sections'] ) {
				$by_term[$args['uncategorized_term_name']] = array();
			} else {
				$by_term[$args['uncategorized_term_name']] = $uncat_posts;
			}
		}
		// Split up this post type's posts by the first alpha character
		$query_args['orderby'] = 'title';
		$query_args['order'] = 'ASC';
		$query_args['tax_query'] = '';
		$by_alpha_posts = get_posts( $query_args );
		foreach( $by_alpha_posts as $post ) {
			if ( preg_match( '/([a-zA-Z])/', $post->post_title, $matches ) == 1 ) {
				$by_alpha[strtoupper($matches[1])][] = $post;
			} else {
				$by_alpha[$args['non_alpha_section_name']][] = $post;
			}
		}
		if( $args['show_empty_sections'] ) {
			foreach( range( 'a', 'z' ) as $letter ) {
				if ( !isset( $by_alpha[strtoupper( $letter )] ) ) {
					$by_alpha[strtoupper( $letter )] = array();
				}
			}
		}
		ksort( $by_alpha );
		$sections = array(
			'resource-search-term'  => $by_term,
			'resource-search-alpha' => $by_alpha,
		);
		ob_start();
	?>
		<div class="resource-search">
			<div class="resource-search-header">
				<form class="resource-search-form" action="." method="get">
					<label class="resource-search-label" for="resource-search-input">
						<h2>
							<?php echo $args['default_search_label']; ?>
						</h2>
					</label>
					<input type="text" id="resource-search-input" name="resource-search-input" class="resource-search-input" placeholder="<?php echo $args['default_search_text']; ?>">
				</form>
			</div>
			<div class="resource-search-results"></div>
			<?php if ( $args['show_sorting'] ) { ?>
			<div class="btn-group resource-search-sorting">
				<button class="btn btn-default<?php if ( $args['default_sorting'] == 'term' ) echo ' active'; ?>">
				</button>
				<button class="btn btn-default<?php if ( $args['default_sorting'] == 'alpha' ) echo ' active'; ?>">
				</button>
			</div>
			<?php } ?>

			<div class="btn-toolbar jump-to-list" role="toolbar" aria-label="Jump To List">
				<div class="btn-group" role="group">
				<?php foreach ( range( 'a', 'm' ) as $alpha ) : ?>
					<a href="#jump-to-<?php echo $alpha; ?>" class="btn btn-default"><?php echo strtoupper( $alpha ); ?></a>
				<?php endforeach; ?>
				</div>
				<div class="btn-group" role="group">
				<?php foreach ( range( 'n', 'z' ) as $alpha ) : ?>
					<a href="#jump-to-<?php echo $alpha; ?>" class="btn btn-default"><?php echo strtoupper( $alpha ); ?></a>
				<?php endforeach; ?>
				</div>
			</div>

		<?php
		foreach ( $sections as $id => $section ):
			$hide = false;
			switch ( $id ) {
				case 'resource-search-alpha':
					if ( $args['default_sorting'] == 'term' ) {
						$hide = True;
					}
					break;
				case 'resource-search-term':
					if ( $args['default_sorting'] == 'alpha' ) {
						$hide = True;
					}
					break;
			}
	?>
			<div class="<?php echo $id; ?>"<?php if ( $hide ) { echo ' style="display:none;"'; } ?>>
				<div class="row">
				<?php
				$count = 0;
				foreach ( $section as $section_title => $section_posts ):
					if ( count( $section_posts ) > 0 || $args['show_empty_sections'] ):
				?>

					<?php if ( $section_title == $args['uncategorized_term_name'] ): ?>
						</div>
							<div class="row">
								<div class="<?php echo $args['column_width']; ?>">
									<div class="resource-search-heading-wrap" id="jump-to-<?php echo strtolower( esc_html( $section_title ) ); ?>">
										<h3 class="resource-search-heading"><?php echo esc_html( $section_title ); ?></h3>
										<hr>
									</div>
								</div>
							</div>

							<div class="row">
							<?php
							// $split_size must be at least 1
							$split_size = max( floor( count( $section_posts ) / $args['column_count'] ), 1 );
							$split_posts = array_chunk( $section_posts, $split_size );
							foreach ( $split_posts as $resource => $column_posts ):
							?>
								<div class="<?php echo $args['column_width']; ?>">
									<ul class="resource-search-list">
									<?php foreach( $column_posts as $key => $post ): ?>
										<li data-post-id="<?php echo $post->ID; ?>">
											<?php echo UCF_Resource_Link_PostType::toHTML( $post ); ?><?php echo $section_title; ?>
										</li>
									<?php endforeach; ?>
									</ul>
								</div>
							<?php endforeach; ?>

					<?php else: ?>

						<?php if ( $count % $args['column_count'] == 0 && $count !== 0 ): ?>
							</div><div class="row">
						<?php endif; ?>

						<div class="<?php echo $args['column_width']; ?>">
							<div class="resource-search-heading-wrap" id="jump-to-<?php echo strtolower( esc_html( $section_title ) ); ?>">
								<h3 class="resource-search-heading"><?php echo esc_html( $section_title ); ?></h3>
								<div class="back-to-top"><a href="#top">Back to Top</a></div></div>
							<hr>
							<ul class="resource-search-list">
							<?php foreach( $section_posts as $post ): ?>
								<li data-post-id="<?php echo $post->ID; ?>">
									<?php echo UCF_Resource_Link_PostType::toHTML( $post ); ?><span class="hidden-xs-up"><?php echo $section_title; ?></span>
								</li>
							<?php endforeach; ?>
							</ul>
						</div>

				<?php
						endif;
					$count++;
					endif;
				endforeach;
				?>
				</div><!-- .row -->
			</div><!-- term/alpha section -->

		<?php endforeach; ?>

		</div><!-- .resource-search -->
	<?php
		$content = ob_get_clean();
		return $script . $content;
	}
}
add_filter( 'ucf_resource_link_display_classic', 'ucf_resource_link_display_classic', 10, 2 );

/**
 * Outputs the content of the resource.
 * Use the `ucf_resource_link_display_classic_after` filter
 * hook to override or modify this output.
 *
 * @author RJ Bruneel
 * @since 1.0.4
 *
 * @param $content WP_Post object | The content
 * @param $args Array | Array of arguments
 *
 * @return string | The html to be appended to output.
 **/
if ( ! function_exists( 'ucf_resource_link_display_classic_after' ) ) {
	function ucf_resource_link_display_classic_after( $content, $args ) {
		ob_start();
	?>
		</article>
	<?php
		return ob_get_clean();
	}
}
add_filter( 'ucf_resource_link_display_classic_after', 'ucf_resource_link_display_classic_after', 10, 2 );

?>
