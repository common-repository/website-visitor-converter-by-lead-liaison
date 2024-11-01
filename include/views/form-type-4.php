<div class="wvc-form__subform">
	<form class="wvc-form__subform-full" data-form="full">
		<?php if ( ! empty( $form_text['ft4'] ) ): ?>
			<p><?php echo esc_html( $form_text['ft4'] ); ?></p>
		<?php endif ?>
		<input type="text" name="first_name" placeholder="<?php esc_html_e( 'First name', 'wvc-forms' ) ?>" required>
		<input type="text" name="last_name" placeholder="<?php esc_html_e( 'Last name', 'wvc-forms' ) ?>" required>
		<input type="text" name="email" placeholder="<?php esc_html_e( 'Email', 'wvc-forms' ) ?>" required>
		<p class="wvc-form__response"></p>
		<button type="submit"><?php echo esc_html( $form_button_text['ft4'] ) ?></button>
	</form>

	<form class="wvc-form__subform-submit-passcode" data-form="submit-passcode">
		<a href="#"><?php esc_html_e( 'Enter Pass Code', 'wvc-forms' ) ?></a>
		<input type="text" name="pass_code" placeholder="<?php esc_html_e( 'Pass Code', 'wvc-forms' ) ?>">
		<p class="wvc-form__response"></p>
		<button type="submit"><?php esc_html_e( 'Submit', 'wvc-forms' ) ?></button>
	</form>
</div>