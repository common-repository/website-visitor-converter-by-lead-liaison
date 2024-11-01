<?php

if ( ! function_exists( 'wvc_submissions_page' ) ) {
	function wvc_submissions_page() {
		add_submenu_page( 'edit.php?post_type=wvc-forms', __('Submissions', 'wvc-forms'), __('Submissions', 'wvc-forms'), 'manage_options', 'wvc-submissions', 'wvc_view_submissions' ); 
	}
}
add_action('admin_menu', 'wvc_submissions_page', 9);


if ( ! function_exists( 'wvc_view_submissions' ) ) {
	function wvc_view_submissions() {
		global $wpdb;
		$table = $wpdb->prefix . 'wvc_emails';

		$list = $wpdb->get_results( "SELECT * from $table order by id desc;", ARRAY_A );

		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title(); ?></h2>

			<div class="wvc-codes-list">
				<?php if ( ! empty( $list ) ): ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Date', 'wvc-forms' ); ?></th>
								<th><?php esc_html_e( 'Form', 'wvc-forms' ); ?></th>
								<th><?php esc_html_e( 'Name', 'wvc-forms' ); ?></th>
								<th><?php esc_html_e( 'Email', 'wvc-forms' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $list as $item ): ?>
								<tr data-code="<?php echo esc_html( $item['code'] ); ?>">
									<td><?php echo date( 'F j, Y g:iA', $item['date'] ); ?></td>
									<td>
										<a href="<?php echo get_edit_post_link( $item['form_id'] ); ?>" data-id="<?php echo esc_attr( $item['form_id'] ); ?>">
											<?php echo get_the_title( $item['form_id'] ); ?>
										</a>
									</td>
									<td><?php echo esc_html( $item['name'] ); ?></td>
									<td><?php echo esc_html( $item['email'] ); ?></td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				<?php else: ?>
					<p><?php esc_html_e( 'No emails found.', 'wvc-forms' ); ?></p>
				<?php endif ?>
			</div>
		</div>
		<?php
	}
}
