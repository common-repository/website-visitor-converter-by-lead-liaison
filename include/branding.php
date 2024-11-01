<?php

if ( ! function_exists( 'wvc_branding' ) ) {
	function wvc_branding() {
		?>
		<div class="wvc-branding">
			<a href="https://www.leadliaison.com/platform-overview/" target="_blank">
				<img src="<?php echo WVC_DIR_URL; ?>/assets/img/Lead-Liaison-App-Promo-Banner.jpg" alt="">
			</a>
		</div>
		<?php
	}
}


if ( ! function_exists( 'wvc_view_branding' ) ) {
	function wvc_view_branding() {
		global $pagenow;

		if ( ( $pagenow == 'post-new.php' || $pagenow == 'edit.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wvc-forms' ) {
			wvc_branding();
		}

		if ( $pagenow == 'post.php' && isset( $_GET['post'] ) && is_numeric( $_GET['post'] ) ) {
			$post_type = get_post_type( $_GET['post'] );
			if ( $post_type == 'wvc-forms' ) {
				wvc_branding();
			}
		}
	}
}
add_action('admin_notices', 'wvc_view_branding');