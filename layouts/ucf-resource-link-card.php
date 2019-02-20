<?php

/**
 * Outputs the content of the resource.
 * Use the `ucf_resource_link_display_card_before` filter
 * hook to override or modify this output.
 *
 * @author RJ Bruneel
 * @since 1.0.4
 *
 * @param $content WP_Post object | The content
 * @param $args Array | Array of arguments
 *
 * @return string | The html to prepend to the output.
 **/
if ( ! function_exists( 'ucf_resource_link_display_card_before' ) ) {
	function ucf_resource_link_display_card_before( $content, $args ) {
		ob_start();
	?>
		<div class="ucf-resource-list-card-wrapper">
	<?php
		return ob_get_clean();
	}
	add_filter( 'ucf_resource_link_display_card_before', 'ucf_resource_link_display_card_before', 10, 2 );
}

/**
 * Outputs the content of the resource.
 * Use the `ucf_resource_link_display_card` filter
 * hook to override or modify this output.
 *
 * @author RJ Bruneel
 * @since 1.0.4
 *
 * @param $content WP_Post object | The content
 * @param $args Array | Array of arguments
 *
 * @return string | The html to be added to output.
 **/
if ( ! function_exists( 'ucf_resource_link_display_card' ) ) {

	function ucf_resource_link_display_card( $content, $args ) {
		$tax_query = null;

		if( !empty( $args['taxonomy'] ) && !empty( $args['resource_link_type_filter'] ) ) {
			$tax_query = array(
				array(
					'taxonomy' => $args['taxonomy'],
					'field'    => 'slug',
					'terms'    => $args['resource_link_type_filter']
				)
			);
		}

		$posts = get_posts(
			array(
				'posts_per_page' => -1,
				'post_type'      => $args['post_type_name'],
				'orderby'        => $args['order_by'],
				'order'          => $args['order'],
				'tax_query'      => $tax_query
			)
		);

		ob_start();
		if ( $posts ):
	?>
			<div class="row">
				<div class="ucf-resource-card-categories col-md-3 mb-4">
				<?php
					$taxonomy = 'resource_link_category';
					$terms = get_terms( array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => true,
					) );

					if ( $terms && !is_wp_error( $terms ) ) :
					?>
						<h2 class="h5 heading-sans-serif text-uppercase mb-3">Filter Directory</h2>
						<ul class="list-unstyled">
							<li><a href="#all">Show All</a></li>
							<?php foreach ( $terms as $term ) { ?>
								<li><a href="#<?php echo $term->slug; ?>"><?php echo $term->name; ?></a></li>
							<?php } ?>
						</ul>
					<?php endif;?>
				</div>
				<div class="col-md-9">
					<div class="ucf-resource-directory-items row">
	<?php
				foreach ( $posts as $key => $post ) :
					$facebook_url = get_post_meta( $post->ID, 'ucf_resource_facebook_url', TRUE );
					$twitter_url = get_post_meta( $post->ID, 'ucf_resource_twitter_url', TRUE );
					$instagram_url = get_post_meta( $post->ID, 'ucf_resource_instagram_url', TRUE );
					$linkedin_url = get_post_meta( $post->ID, 'ucf_resource_linkedin_url', TRUE );
					$youtube_url = get_post_meta( $post->ID, 'ucf_resource_youtube_url', TRUE );

					$terms = get_the_terms( $post, $taxonomy );
					if( !empty( $terms ) ) {
						$terms = implode(' ', array_map(function($x) { return $x->slug; }, $terms));
					} else {
						$terms = '';
					}
	?>
					<div class="card-wrapper col-md-6 col-lg-4 mb-4 <?php echo $terms; ?>">
						<div class="card h-100">
							<div class="card-block pb-0">
								<a href="<?php echo get_post_meta( $post->ID, 'ucf_resource_link_url', TRUE ); ?>">
									<h4 class="ucf-resource-link-title card-title text-center h6"><?php echo $post->post_title; ?></h4>
								</a>
							</div>
							<div class="card-block ucf-resource-social-icons text-center pt-0">
								<?php if( $facebook_url ) : ?>
									<a class="ucf-resource-social-link bg-default bg-default-link ucf-resource-social-facebook" target="_blank" href="<?php echo $facebook_url ?>">
										<span class="fa fa-facebook" aria-hidden="true"></span>
										<p class="sr-only">Like us on Facebook</p>
									</a>
								<?php endif; ?>
								<?php if( $twitter_url ) : ?>
									<a class="ucf-resource-social-link bg-default bg-default-link ucf-resource-social-twitter" target="_blank" href="<?php echo $twitter_url ?>">
										<span class="fa fa-twitter" aria-hidden="true"></span>
										<p class="sr-only">Follow us on Twitter</p>
									</a>
								<?php endif; ?>
								<?php if( $instagram_url ) : ?>
									<a class="ucf-resource-social-link bg-default bg-default-link ucf-resource-social-instagram" target="_blank" href="<?php echo $instagram_url ?>">
										<span class="fa fa-instagram" aria-hidden="true"></span>
										<p class="sr-only">Find us on Instagram</p>
									</a>
								<?php endif; ?>
								<?php if( $linkedin_url ) : ?>
									<a class="ucf-resource-social-link bg-default bg-default-link ucf-resource-social-linkedin" target="_blank" href="<?php echo $linkedin_url ?>">
										<span class="fa fa-linkedin" aria-hidden="true"></span>
										<p class="sr-only">View our LinkedIn page</p>
									</a>
								<?php endif; ?>
								<?php if( $youtube_url ) : ?>
									<a class="ucf-resource-social-link bg-default bg-default-link ucf-resource-social-youtube" target="_blank" href="<?php echo $youtube_url ?>">
										<span class="fa fa-youtube" aria-hidden="true"></span>
										<p class="sr-only">Follow us on YouTube</p>
									</a>
								<?php endif; ?>
							</div>
						</div>
					</div>
	<?php
			endforeach;
	?>
					</div>
				</div>
			</div>
	<?php
		else:
	?>
			<div class="ucf-resource-list-error">No results found.</div>
	<?php
		endif;

		return ob_get_clean();
	}
	add_filter( 'ucf_resource_link_display_card', 'ucf_resource_link_display_card', 10, 2 );
}

/**
 * Outputs the content of the resource.
 * Use the `ucf_resource_link_display_card_after` filter
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
if ( ! function_exists( 'ucf_resource_link_display_card_after' ) ) {
	function ucf_resource_link_display_card_after( $content, $args ) {
		ob_start();
	?>
		</div>
	<?php
		return ob_get_clean();
	}
	add_filter( 'ucf_resource_link_display_card_after', 'ucf_resource_link_display_card_after', 10, 2 );
}
