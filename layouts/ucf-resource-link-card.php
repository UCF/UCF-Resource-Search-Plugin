<?php

if ( ! function_exists( 'ucf_resource_link_display_card_before' ) ) {
	function ucf_resource_link_display_card_before( $content, $args ) {
		ob_start();
	?>
		<div class="ucf-resource-list-topic-list-wrapper">
	<?php
		return ob_get_clean();
	}
	add_filter( 'ucf_resource_link_display_card_before', 'ucf_resource_link_display_card_before', 10, 2 );
}

if ( ! function_exists( 'ucf_resource_link_display_card' ) ) {

	function ucf_resource_link_display_card( $content, $args ) {
		$posts = get_posts( array(
			'posts_per_page' => -1,
			'post_type'      => $args['post_type_name'],
			'orderby'        => $args['order_by'],
			'sort_order'     => $args['order'] )
		);

		ob_start();
		if ( $posts ):
	?>
			<div class="row">
	<?php
			foreach ( $posts as $key => $post ) :
				$facebook_url = get_post_meta( $post->ID, 'ucf_resource_facebook_url', TRUE );
				$twitter_url = get_post_meta( $post->ID, 'ucf_resource_twitter_url', TRUE );
				$instagram_url = get_post_meta( $post->ID, 'ucf_resource_instagram_url', TRUE );
				$linkedin_url = get_post_meta( $post->ID, 'ucf_resource_linkedin_url', TRUE );
				$youtube_url = get_post_meta( $post->ID, 'ucf_resource_youtube_url', TRUE );
	?>
				<div class="col-md-6 col-lg-4 mb-4">
					<div class="card h-100">
						<div class="card-block pb-0">
							<a href="<?php echo get_post_meta( $post->ID, 'ucf_resource_link_url', TRUE ); ?>">
								<h4 class="ucf-resource-link-title card-title text-center"><?php echo $post->post_title; ?></h4>
							</a>
						</div>
						<div class="card-block ucf-social-icons text-center pt-0">
							<?php if( $facebook_url ) : ?>
								<a class="ucf-social-link btn-facebook sm color" target="_blank" href="<?php echo $facebook_url ?>">
									<span class="fa fa-facebook" aria-hidden="true"></span>
									<p class="sr-only">Like us on Facebook</p>
								</a>
							<?php endif; ?>
							<?php if( $twitter_url ) : ?>
								<a class="ucf-social-link btn-twitter sm color" target="_blank" href="<?php echo $twitter_url ?>">
									<span class="fa fa-twitter" aria-hidden="true"></span>
									<p class="sr-only">Follow us on Twitter</p>
								</a>
							<?php endif; ?>
							<?php if( $instagram_url ) : ?>
								<a class="ucf-social-link btn-instagram sm color" target="_blank" href="<?php echo $instagram_url ?>">
									<span class="fa fa-instagram" aria-hidden="true"></span>
									<p class="sr-only">Find us on Instagram</p>
								</a>
							<?php endif; ?>
							<?php if( $linkedin_url ) : ?>
								<a class="ucf-social-link btn-linkedin sm color" target="_blank" href="<?php echo $linkedin_url ?>">
									<span class="fa fa-linkedin" aria-hidden="true"></span>
									<p class="sr-only">View our LinkedIn page</p>
								</a>
							<?php endif; ?>
							<?php if( $youtube_url ) : ?>
								<a class="ucf-social-link btn-youtube sm color" target="_blank" href="<?php echo $youtube_url ?>">
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
