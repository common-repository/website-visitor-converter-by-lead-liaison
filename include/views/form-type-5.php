<div class="wvc-form__subform">
	<form class="wvc-form__subform-email" data-form="email" data-close="close">
		<?php if ( ! empty( $form_text['ft5'] ) ): ?>
			<p><?php echo esc_html( $form_text['ft5'] ); ?></p>
		<?php endif ?>
		<input type="text" name="email" required>
		<p class="wvc-form__response"></p>
		<button type="submit"><?php echo esc_html( $form_button_text['ft5'] ) ?></button>
	</form>
</div>