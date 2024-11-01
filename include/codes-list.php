<?php

if ( ! function_exists( 'wvc_codes_list_page' ) ) {
	function wvc_codes_list_page() {
		add_submenu_page( 'edit.php?post_type=wvc-forms', __('WVC Codes', 'wvc-forms'), __('Codes', 'wvc-forms'), 'manage_options', 'wvc-codes', 'wvc_view_codes_page' ); 
	}
}
add_action( 'admin_menu', 'wvc_codes_list_page', 9 );


if ( ! function_exists( 'wvc_view_codes_page' ) ) {
	function wvc_view_codes_page() {
		$codes = (new WVC_Codes)->get_list();

		$args = array(
			'post_type' => 'wvc-forms',
			'posts_per_page' => -1,
		);

		$forms_query = new WP_Query( $args );
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title(); ?></h2>

			<?php if ( $forms_query->have_posts() ): ?>
				<div class="wvc-generate-codes">
					<form method="post">
						<?php esc_html_e( 'Generate New Codes:', 'wvc-forms' ); ?>
						<input type="number" min="1" max="10" name="count" placeholder="<?php esc_html_e( 'Count', 'wvc-forms' ); ?>" required>
						<input type="text" name="expire" class="wvc-expire-date" autocomplete="off" placeholder="<?php esc_html_e( 'Expiration', 'wvc-forms' ); ?>">
						<select name="form_id">
							<option value="0"><?php esc_html_e( 'Choose a form', 'wvc-forms' ); ?></option>
							<?php while ( $forms_query->have_posts() ) : $forms_query->the_post(); ?>
								<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
							<?php endwhile; wp_reset_postdata(); ?>
						</select>
						<input type="number" min="0" max="9999999" name="limitation" autocomplete="off" placeholder="<?php esc_html_e( 'Usage Limitation', 'wvc-forms' ); ?>">
						<button class="button"><?php esc_html_e( 'Generate', 'wvc-forms' ); ?></button>
						<a href="#" class="button js-add-code">Add manually</a>
					</form>
				</div>
			<?php else: ?>
				<p><?php printf( esc_html__( 'You need to %s at least one form to generate codes.', 'wvc-forms' ), '<a href="' . admin_url( "post-new.php?post_type=wvc-forms" ) . '">create</a>'); ?></p>
			<?php endif ?>

			<!-- Add code form -->
			<div class="wvc-add-code">
				<form>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Code', 'wvc-forms' ); ?></label>
						<input type="text" name="code" pattern="[0-9]{6,10}" required title="<?php esc_html_e( 'Numbers only, 6 to 10 characters', 'wvc-forms' ); ?>">
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Expiration', 'wvc-forms' ); ?></label>
						<input type="text" name="expire" class="wvc-expire-date" autocomplete="off">
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Status', 'wvc-forms' ); ?></label>
						<select name="status">
							<option value="yes"><?php esc_html_e( 'Not used', 'wvc-forms' ); ?></option>
							<option value="no"><?php esc_html_e( 'Used', 'wvc-forms' ); ?></option>
						</select>
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Form', 'wvc-forms' ); ?></label>
						<select name="form_id">
							<option value="0"><?php esc_html_e( 'Choose a form', 'wvc-forms' ); ?></option>
							<?php while ( $forms_query->have_posts() ) : $forms_query->the_post(); ?>
								<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
							<?php endwhile; wp_reset_postdata(); ?>
						</select>
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Usage Limitation', 'wvc-forms' ); ?></label>
						<input type="number" min="0" max="9999999" name="limitation" autocomplete="off">
					</div>
					<div class="wvc-form-row">
						<button type="reset" class="button cancel"><?php esc_html_e( 'Cancel', 'wvc-forms' ); ?></button>
						<button type="submit" class="button save"><?php esc_html_e( 'Save', 'wvc-forms' ); ?></button>
					</div>
					<div class="wvc-add-code__msg"></div>	
				</form>
			</div>
			<!-- End add code form -->

			<!-- Edit code form -->
			<div class="wvc-edit-code">
				<form>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Code', 'wvc-forms' ); ?></label>
						<input type="text" name="code" readonly>
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Expiration', 'wvc-forms' ); ?></label>
						<input type="text" name="expire" autocomplete="off">
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Status', 'wvc-forms' ); ?></label>
						<select name="status">
							<option value="yes"><?php esc_html_e( 'Not used', 'wvc-forms' ); ?></option>
							<option value="no"><?php esc_html_e( 'Used', 'wvc-forms' ); ?></option>
						</select>
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Form', 'wvc-forms' ); ?></label>
						<select name="form_id">
							<option value="0"><?php esc_html_e( 'Choose a form', 'wvc-forms' ); ?></option>
							<?php while ( $forms_query->have_posts() ) : $forms_query->the_post(); ?>
								<option value="<?php the_ID(); ?>"><?php the_title(); ?></option>
							<?php endwhile; wp_reset_postdata(); ?>
						</select>
					</div>
					<div class="wvc-form-row">
						<label><?php esc_html_e( 'Usage Limitation', 'wvc-forms' ); ?></label>
						<input type="number" min="0" max="9999999" name="limitation" autocomplete="off">
					</div>
					<div class="wvc-form-row">
						<button type="reset" class="button cancel"><?php esc_html_e( 'Cancel', 'wvc-forms' ); ?></button>
						<button type="submit" class="button save"><?php esc_html_e( 'Save', 'wvc-forms' ); ?></button>
					</div>
				</form>
			</div>
			<!-- End edit code form -->

			<div class="wvc-codes-list">
				<?php if ( ! empty( $codes ) ): ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Code', 'wvc-forms' ); ?></th>
								<th><?php esc_html_e( 'Expiration', 'wvc-forms' ); ?></th>
								<th><?php esc_html_e( 'Form', 'wvc-forms' ); ?></th>
								<th><?php esc_html_e( 'Status', 'wvc-forms' ); ?></th>
								<th><?php esc_html_e( 'Usage Limitation', 'wvc-forms' ); ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $codes as $code_item ):
								$status_cell = $code_item['status'] == 'yes' ? esc_html__( 'Not used', 'wvc-forms' ) : esc_html__( 'Used', 'wvc-forms' );
								if ( $code_item['limitation'] != '' ) {
									$status_cell = $code_item['used_times'] . '/' . $code_item['limitation'];
								} ?>
								<tr data-code="<?php echo esc_html( $code_item['code'] ); ?>">
									<td><?php echo esc_html( $code_item['code'] ); ?></td>
									<td><?php echo is_numeric( $code_item['valid_to'] ) && $code_item['valid_to'] > 0 ? date('F j, Y', $code_item['valid_to']) : ''; ?></td>
									<td>
										<a href="<?php echo get_edit_post_link( $code_item['form_id'] ); ?>" data-id="<?php echo esc_attr( $code_item['form_id'] ); ?>">
											<?php echo get_the_title( $code_item['form_id'] ); ?>
										</a>
									</td>
									<td><?php echo esc_html( $status_cell ); ?></td>
									<td><?php echo $code_item['limitation'] == '' ? esc_html__( 'No limit', 'wvc-forms' ) : esc_html( $code_item['limitation'] ); ?></td>
									<td>
										<a href="#" class="edit"><?php esc_html_e( 'Edit', 'wvc-forms' ); ?></a> | 
										<a href="#" class="delete"><?php esc_html_e( 'Delete', 'wvc-forms' ); ?></a>
									</td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				<?php else: ?>
					<p><?php esc_html_e( 'No codes found.', 'wvc-forms' ); ?></p>
				<?php endif ?>
				
			</div>
		</div>
		<?php
	}
}

