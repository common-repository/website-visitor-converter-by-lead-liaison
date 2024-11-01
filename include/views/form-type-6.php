<div class="wvc-form__subform">
	<form class="wvc-form__subform-full" data-form="full" data-close="close">
		<?php if ( ! empty( $form_text['ft6'] ) ): ?>
			<p><?php echo esc_html( $form_text['ft6'] ); ?></p>
		<?php endif ?>
		<input type="text" name="first_name" placeholder="<?php esc_html_e( 'First name', 'wvc-forms' ) ?>" required>
		<input type="text" name="last_name" placeholder="<?php esc_html_e( 'Last name', 'wvc-forms' ) ?>" required>
		<input type="text" name="email" placeholder="<?php esc_html_e( 'Email', 'wvc-forms' ) ?>" required>
		<p class="wvc-form__response"></p>
		<button type="submit"><?php echo esc_html( $form_button_text['ft6'] ) ?></button>
	</form>
</div>