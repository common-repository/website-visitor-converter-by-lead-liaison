<div class="wvc-form__subform">
	<form class="wvc-form__subform-passcode" data-form="passcode">
		<?php if ( ! empty( $form_text['ft1'] ) ): ?>
			<p><?php echo esc_html( $form_text['ft1'] ); ?></p>
		<?php endif ?>
		<input type="text" name="pass_code" placeholder="<?php esc_html_e( 'Pass Code', 'wvc-forms' ) ?>" required>
		<p class="wvc-form__response"></p>
		<button type="submit"><?php echo esc_html( $form_button_text['ft1'] ) ?></button>
	</form>

	<form class="wvc-form__subform-request-passcode" data-form="request-passcode">
		<a href="#"><?php esc_html_e( 'Request Pass Code', 'wvc-forms' ) ?></a>
		<input type="email" name="email" placeholder="<?php esc_html_e( 'Email', 'wvc-forms' ) ?>">
		<p class="wvc-form__response"></p>
		<button type="submit"><?php esc_html_e( 'Request', 'wvc-forms' ) ?></button>
	</form>
</div>