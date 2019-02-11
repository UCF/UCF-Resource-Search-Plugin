<?php

if ( ! function_exists( 'ucf_resource_link_display_card_before' ) ) {
	function ucf_resource_link_display_card_before( $content, $args ) {
		ob_start();
	?>
		<div class="ucf-resource-list-topic-list-wrapper">
	<?php
		return ob_get_clean();
	}
	add_filter( 'ucf_resource_link_display_card_before', 'ucf_resource_link_display_card_before', 10, 3 );
}

if ( ! function_exists( 'ucf_resource_link_display_card_title' ) ) {
	function ucf_resource_link_display_card_title( $content, $args ) {
		$formatted_title = '';
		if ( $title = $args['title'] ) {
			$formatted_title = '<h2 class="ucf-resource-list-title">' . $title . '</h2>';
		}
		return $formatted_title;
	}
	add_filter( 'ucf_resource_link_display_card_title', 'ucf_resource_link_display_card_title', 10, 3 );
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
	?>
				<div class="col-md-6 col-lg-4 mb-4">
					<div class="card h-100">
						<div class="card-block pb-0">
							<a href="<?php echo get_post_meta( $post->ID, 'ucf_resource_link_url', TRUE ); ?>">
								<h4 class="ucf-resource-link-title card-title text-center"><?php echo $post->post_title; ?></h4>
							</a>
						</div>
						<div class="card-block text-center pt-0">
							<a href="#" class="btn btn-secondary card-link fa fa-facebook"></a>
							<a href="#" class="btn btn-secondary card-link fa fa-instagram"></a>
							<a href="#" class="btn btn-secondary card-link fa fa-youtube"></a>
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
	add_filter( 'ucf_resource_link_display_card', 'ucf_resource_link_display_card', 10, 3 );
}

if ( ! function_exists( 'ucf_resource_link_display_card_after' ) ) {
	function ucf_resource_link_display_card_after( $content, $args ) {
		ob_start();
	?>
		</div>
	<?php
		return ob_get_clean();
	}
	add_filter( 'ucf_resource_link_display_card_after', 'ucf_resource_link_display_card_after', 10, 3 );
}
