<?php


// Add custom column
if ( ! function_exists( 'wvc_custom_post_type_columns' ) ) {
	function wvc_custom_post_type_columns( $columns ) {
		$columns['shortcode'] = esc_html__( 'Shortcode', 'wvc-forms' );
		return $columns;
	}
}
add_filter('manage_wvc-forms_posts_columns', 'wvc_custom_post_type_columns');


if ( ! function_exists( 'wvc_fill_custom_post_type_columns' ) ) {
	function wvc_fill_custom_post_type_columns( $column, $post_id ) {
		if ( $column == 'shortcode' ) {
			echo '[wvc_form id=&#34;' . $post_id . '&#34;]';
		}
	}
}
add_action( 'manage_wvc-forms_posts_custom_column' , 'wvc_fill_custom_post_type_columns', 10, 2 );


if ( ! function_exists( 'wvc_add_custom_box' ) ) {
	function wvc_add_custom_box(){
		// Shortcode
		add_meta_box( 'wvc_block_shortcode', 
			esc_html__('Block Shortcode', 'wvc-forms' ), 
			'wvc_view_shortcode_block', 
			'wvc-forms', 
			'side' 
		);	

		// Main options
		add_meta_box( 'wvc_main_options', 
			esc_html__('Main Options', 'wvc-forms' ), 
			'wvc_view_main_options', 
			'wvc-forms'
		);

		// Texts
		add_meta_box( 'wvc_text_options', 
			esc_html__('Text Options', 'wvc-forms' ), 
			'wvc_view_text_options', 
			'wvc-forms'
		);	

		// Styles
		add_meta_box( 'wvc_style_options', 
			esc_html__('Form Styles', 'wvc-forms' ), 
			'wvc_view_style_options', 
			'wvc-forms'
		);
	}
}
add_action('add_meta_boxes', 'wvc_add_custom_box');


if ( ! function_exists( 'wvc_view_style_options' ) ) {
	function wvc_view_style_options( $post, $meta ) {
		$style  = get_post_meta( $post->ID, 'style', true );

		$b_b_c  = get_post_meta( $post->ID, 'b_b_c', true );
		$b_t_c  = get_post_meta( $post->ID, 'b_t_c', true );
		$f_tt_c = get_post_meta( $post->ID, 'f_tt_c', true );
		$f_st_c = get_post_meta( $post->ID, 'f_st_c', true );
		$f_bg_c = get_post_meta( $post->ID, 'f_bg_c', true );
		$f_br_c = get_post_meta( $post->ID, 'f_br_c', true );
		?>
		<div class="wvc-options-block__wrap js-wvc-options-block__wrap">
			<table>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Form Overlay Style:', 'wvc-forms' ) ?></th>
					<td>
						<select name="style">
							<option value="light" <?php selected( $style, 'light' ) ?>><?php esc_html_e( 'Light', 'wvc-forms' ) ?></option>
							<option value="dark" <?php selected( $style, 'dark' ) ?>><?php esc_html_e( 'Dark', 'wvc-forms' ) ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Button Background Color:', 'wvc-forms' ) ?></th>
					<td class="wvc-va-center"><input type="text" name="b_b_c" class="wvc-color-picker" value="<?php echo esc_attr( $b_b_c ); ?>"></td>
				</tr>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Button Text Color:', 'wvc-forms' ) ?></th>
					<td class="wvc-va-center"><input type="text" name="b_t_c" class="wvc-color-picker" value="<?php echo esc_attr( $b_t_c ); ?>"></td>
				</tr>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Form Title Text Color:', 'wvc-forms' ) ?></th>
					<td class="wvc-va-center"><input type="text" name="f_tt_c" class="wvc-color-picker" value="<?php echo esc_attr( $f_tt_c ); ?>"></td>
				</tr>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Form Sub-Text Color:', 'wvc-forms' ) ?></th>
					<td class="wvc-va-center"><input type="text" name="f_st_c" class="wvc-color-picker" value="<?php echo esc_attr( $f_st_c ); ?>"></td>
				</tr>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Form Background Color:', 'wvc-forms' ) ?></th>
					<td class="wvc-va-center"><input type="text" name="f_bg_c" class="wvc-color-picker" value="<?php echo esc_attr( $f_bg_c ); ?>"></td>
				</tr>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Form Border Color:', 'wvc-forms' ) ?></th>
					<td class="wvc-va-center"><input type="text" name="f_br_c" class="wvc-color-picker" value="<?php echo esc_attr( $f_br_c ); ?>"></td>
				</tr>
			</table>
		</div>

		<?php
	}
}


if ( ! function_exists( 'wvc_view_shortcode_block' ) ) {
	function wvc_view_shortcode_block( $post, $meta ){
		echo '<input type="text" value="[wvc_form id=&#34;' . $post->ID . '&#34;]" size="25" readonly />';
	}
}


if ( ! function_exists( 'wvc_view_text_options' ) ) {
	function wvc_view_text_options( $post, $meta ) {

		wp_nonce_field( plugin_basename(__FILE__), 'myplugin_noncename' );

		$form_title 	  = get_post_meta( $post->ID, 'wvc_form_title', true );
		$form_text 		  = get_post_meta( $post->ID, 'wvc_form_text', true );
		$form_button_text = get_post_meta( $post->ID, 'wvc_form_button_text', true );

		$meta = get_post_meta( $post->ID );
		if ( empty( $meta['wvc_form_title'] ) ) {

			$form_title = array();
			$form_title['ft1'] = esc_html__( 'Enter Credentials', 'wvc-forms' );
			$form_title['ft2'] = esc_html__( 'Request Access', 'wvc-forms' );
			$form_title['ft3'] = esc_html__( 'Enter Credentials', 'wvc-forms' );
			$form_title['ft4'] = esc_html__( 'Request Access', 'wvc-forms' );
			$form_title['ft5'] = esc_html__( 'Enter Credentials', 'wvc-forms' );
			$form_title['ft6'] = esc_html__( 'Enter Credentials', 'wvc-forms' );

			$form_text = array();
			$form_text['ft1'] = esc_html__( 'Enter Pass Code to view content', 'wvc-forms' );
			$form_text['ft2'] = esc_html__( 'Enter email address to request access', 'wvc-forms' );
			$form_text['ft3'] = esc_html__( 'Enter Pass Code and email address to view content', 'wvc-forms' );
			$form_text['ft4'] = esc_html__( 'Enter your contact information to request access', 'wvc-forms' );
			$form_text['ft5'] = esc_html__( 'Enter email address to view content', 'wvc-forms' );
			$form_text['ft6'] = esc_html__( 'Enter your contact information to view content', 'wvc-forms' );

			$form_button_text = array();
			$form_button_text['ft1'] = esc_html__( 'Submit', 'wvc-forms' );
			$form_button_text['ft2'] = esc_html__( 'Submit', 'wvc-forms' );
			$form_button_text['ft3'] = esc_html__( 'Submit', 'wvc-forms' );
			$form_button_text['ft4'] = esc_html__( 'Submit', 'wvc-forms' );
			$form_button_text['ft5'] = esc_html__( 'Submit', 'wvc-forms' );
			$form_button_text['ft6'] = esc_html__( 'Submit', 'wvc-forms' );
		}

		?>
		<div class="wvc-options-block__wrap js-wvc-options-block__wrap">
			<table>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Form Title:', 'wvc-forms' ) ?></th>
					<td>
						<input type="text" class="wvc-ft1" name="wvc_form_title[ft1]" value="<?php echo esc_html( $form_title['ft1'] ); ?>">
						<input type="text" class="wvc-ft2" name="wvc_form_title[ft2]" value="<?php echo esc_html( $form_title['ft2'] ); ?>">
						<input type="text" class="wvc-ft3" name="wvc_form_title[ft3]" value="<?php echo esc_html( $form_title['ft3'] ); ?>">
						<input type="text" class="wvc-ft4" name="wvc_form_title[ft4]" value="<?php echo esc_html( $form_title['ft4'] ); ?>">
						<input type="text" class="wvc-ft5" name="wvc_form_title[ft5]" value="<?php echo esc_html( $form_title['ft5'] ); ?>">
						<input type="text" class="wvc-ft6" name="wvc_form_title[ft6]" value="<?php echo esc_html( $form_title['ft6'] ); ?>">
					</td>
				</tr>
				<tr>
					<th><?php esc_html_e( 'Form Text:', 'wvc-forms' ) ?></th>
					<td>
						<textarea class="wvc-ft1" cols="30" rows="5" name="wvc_form_text[ft1]"><?php echo esc_html( $form_text['ft1'] ); ?></textarea>
						<textarea class="wvc-ft2" cols="30" rows="5" name="wvc_form_text[ft2]"><?php echo esc_html( $form_text['ft2'] ); ?></textarea>
						<textarea class="wvc-ft3" cols="30" rows="5" name="wvc_form_text[ft3]"><?php echo esc_html( $form_text['ft3'] ); ?></textarea>
						<textarea class="wvc-ft4" cols="30" rows="5" name="wvc_form_text[ft4]"><?php echo esc_html( $form_text['ft4'] ); ?></textarea>
						<textarea class="wvc-ft5" cols="30" rows="5" name="wvc_form_text[ft5]"><?php echo esc_html( $form_text['ft5'] ); ?></textarea>
						<textarea class="wvc-ft6" cols="30" rows="5" name="wvc_form_text[ft6]"><?php echo esc_html( $form_text['ft6'] ); ?></textarea>
					</td>
				</tr>
				<tr>
					<th class="wvc-va-center"><?php esc_html_e( 'Form Button Text:', 'wvc-forms' ) ?></th>
					<td>
						<input class="wvc-ft1" type="text" name="wvc_form_button_text[ft1]" value="<?php echo esc_html( $form_button_text['ft1'] ); ?>">
						<input class="wvc-ft2" type="text" name="wvc_form_button_text[ft2]" value="<?php echo esc_html( $form_button_text['ft2'] ); ?>">
						<input class="wvc-ft3" type="text" name="wvc_form_button_text[ft3]" value="<?php echo esc_html( $form_button_text['ft3'] ); ?>">
						<input class="wvc-ft4" type="text" name="wvc_form_button_text[ft4]" value="<?php echo esc_html( $form_button_text['ft4'] ); ?>">
						<input class="wvc-ft5" type="text" name="wvc_form_button_text[ft5]" value="<?php echo esc_html( $form_button_text['ft5'] ); ?>">
						<input class="wvc-ft6" type="text" name="wvc_form_button_text[ft6]" value="<?php echo esc_html( $form_button_text['ft6'] ); ?>">
					</td>
				</tr>
			</table>
		</div>
		<?php
	}
}


if ( ! function_exists( 'wvc_view_main_options' ) ) {
	function wvc_view_main_options( $post, $meta ) {
		$options = get_option( 'wvc_plugin' );
		$disabled = '';
		if ( empty( $options['leadliaison_key'] ) ) {
			$disabled = 'disabled';
		}

		$type = get_post_meta( $post->ID, 'type', true );
		$type = ! empty( $type ) && is_numeric( $type ) ? $type : '1';
		$automation_id = get_post_meta( $post->ID, 'automation_id', true );
		?>
		<div class="wvc-options-block__wrap">
			<table>
				<tr>
					<th>Type:</th>
					<td>
						<label><input type="radio" name="type" value="5" <?php echo $type == '5' ? 'checked' : ''; ?>> <?php esc_html_e( 'Email Gate (No Pass Code)', 'wvc-forms' ) ?></label>
						<label><input type="radio" name="type" value="6" <?php echo $type == '6' ? 'checked' : ''; ?>> <?php esc_html_e( 'Name/Email Gate (No Pass Code)', 'wvc-forms' ) ?></label>
						<label><input <?php echo $disabled ?> type="radio" name="type" value="1" <?php echo $type == '1' ? 'checked' : ''; ?>> <?php esc_html_e( 'Pass Code Prompt', 'wvc-forms' ) ?></label>
						<label><input <?php echo $disabled ?> type="radio" name="type" value="2" <?php echo $type == '2' ? 'checked' : ''; ?>> <?php esc_html_e( 'Email to Request Access', 'wvc-forms' ) ?></label>
						<label><input <?php echo $disabled ?> type="radio" name="type" value="3" <?php echo $type == '3' ? 'checked' : ''; ?>> <?php esc_html_e( 'Pass Code with Email Prompt', 'wvc-forms' ) ?></label>
						<label><input <?php echo $disabled ?> type="radio" name="type" value="4" <?php echo $type == '4' ? 'checked' : ''; ?>> <?php esc_html_e( 'Name/Email to Request Access', 'wvc-forms' ) ?></label>
					</td>
				</tr>
				<tr class="wvc-automation-id-field">
					<th class="wvc-va-center"><?php esc_html_e( 'Automation ID:', 'wvc-forms' ) ?></th>
					<td>
						<input type="text" name="automation_id" value="<?php echo esc_attr( $automation_id ) ?>">
					</td>
				</tr>
			</table>
		</div>
		<?php
	}
}



if ( ! function_exists( 'wvc_save_postdata' ) ) {
	function wvc_save_postdata( $post_id ) {
		if ( ! isset( $_POST['myplugin_noncename'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename(__FILE__) ) ) {
			return;
		}

		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return;
		}

		if( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}


		$form_title = isset( $_POST['wvc_form_title'] ) ? (array) $_POST['wvc_form_title'] : array();
		$form_title = array_map( 'sanitize_title', $form_title );

		$form_text = isset( $_POST['wvc_form_text'] ) ? (array) $_POST['wvc_form_text'] : array();
		$form_text = array_map( 'sanitize_title', $form_text );
		
		$form_button_text = isset( $_POST['wvc_form_button_text'] ) ? (array) $_POST['wvc_form_button_text'] : array();
		$form_button_text = array_map( 'sanitize_title', $form_button_text );

		$style 		   = sanitize_text_field( $_POST['style'] );
		$type 		   = sanitize_text_field( $_POST['type'] );
		$automation_id = sanitize_text_field( $_POST['automation_id'] );

		$b_b_c  = sanitize_text_field( $_POST['b_b_c'] );
		$b_t_c  = sanitize_text_field( $_POST['b_t_c'] );
		$f_tt_c = sanitize_text_field( $_POST['f_tt_c'] );
		$f_st_c = sanitize_text_field( $_POST['f_st_c'] );
		$f_bg_c = sanitize_text_field( $_POST['f_bg_c'] );
		$f_br_c = sanitize_text_field( $_POST['f_br_c'] );

		update_post_meta( $post_id, 'wvc_form_title', $form_title );
		update_post_meta( $post_id, 'wvc_form_text', $form_text );
		update_post_meta( $post_id, 'wvc_form_button_text', $form_button_text );

		update_post_meta( $post_id, 'style', $style );
		update_post_meta( $post_id, 'type', $type );
		update_post_meta( $post_id, 'automation_id', $automation_id );

		update_post_meta( $post_id, 'b_b_c', $b_b_c );
		update_post_meta( $post_id, 'b_t_c', $b_t_c );
		update_post_meta( $post_id, 'f_tt_c', $f_tt_c );
		update_post_meta( $post_id, 'f_st_c', $f_st_c );
		update_post_meta( $post_id, 'f_bg_c', $f_bg_c );
		update_post_meta( $post_id, 'f_br_c', $f_br_c );
	}
}
add_action( 'save_post', 'wvc_save_postdata' );