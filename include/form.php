<?php

if ( ! function_exists( 'wvc_view_form' ) ) {
	function wvc_view_form( $atts, $content = '' ) {
		if ( isset( $atts['id'] ) && is_numeric( $atts['id'] ) && wvc_post_exists( $atts['id'] ) ) {
			$post_id = $atts['id'];
			$form_id = 'wvc-form__' . $post_id;

			$form_title 	  = get_post_meta( $post_id, 'wvc_form_title', true );
			$form_text 		  = get_post_meta( $post_id, 'wvc_form_text', true );
			$form_button_text = get_post_meta( $post_id, 'wvc_form_button_text', true );
			
			$style 			  = get_post_meta( $post_id, 'style', true );
			$type 			  = get_post_meta( $post_id, 'type', true );

			$styles = '';
			$b_b_c  = get_post_meta( $post_id, 'b_b_c', true );
			$b_t_c  = get_post_meta( $post_id, 'b_t_c', true );
			$f_tt_c = get_post_meta( $post_id, 'f_tt_c', true );
			$f_st_c = get_post_meta( $post_id, 'f_st_c', true );
			$f_bg_c = get_post_meta( $post_id, 'f_bg_c', true );
			$f_br_c = get_post_meta( $post_id, 'f_br_c', true );

			if ( ! empty( $b_b_c ) ) {
				$styles .= '.' . $form_id . ' button { background-color: ' . $b_b_c . ';}';
			}

			if ( ! empty( $b_b_c ) ) {
				$styles .= '.' . $form_id . ' button { color: ' . $b_t_c . ';}';
			}

			if ( ! empty( $f_tt_c ) ) {
				$styles .= '.' . $form_id . ' .wvc-form__head { color: ' . $f_tt_c . ';}';
			}

			if ( ! empty( $f_bg_c ) ) {
				$styles .= '.' . $form_id . ' .wvc-form__content { background-color: ' . $f_bg_c . ';}';
			}
			if ( ! empty( $f_br_c ) ) {
				list($r, $g, $b) = sscanf($f_br_c, "#%02x%02x%02x");
				$styles .= '.' . $form_id . ' .wvc-form__content { box-shadow: 0px 0px 4px 0px rgba(' . $r . ', ' . $g . ', ' . $b . ', 0.75);}';
			}

			if ( ! empty( $f_st_c ) ) {
				$styles .= '.' . $form_id . ' a { color: ' . $f_st_c . ';}';
				$styles .= '.' . $form_id . ' p { color: ' . $f_st_c . ';}';
				$styles .= '.' . $form_id . ' p.wvc-form__response[data-type=success] { color: ' . $f_st_c . ';}';
			}

			if ( ! empty( $styles ) ) {
				wp_register_style( 'wvc-styles__form' . $post_id, false );
				wp_enqueue_style( 'wvc-styles__form' . $post_id );
				wp_add_inline_style( 'wvc-styles__form' . $post_id, $styles );
			}


			$classes = array( 'wvc-form' );
			$classes[] = $form_id;
			$classes[] = 'wvc-form__' . $style;
			$classes[] = 'wvc-form__type-' . $type;

			ob_start();
			?>
			<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" data-id="<?php echo esc_attr( $post_id ); ?>">
				<div class="wvc-form__overlay"></div>
				<div class="wvc-form__content">
					<div class="wvc-form__head">
						<?php echo esc_html( $form_title['ft' . $type] ) ?>
					</div>

					<div class="wvc-form__body">	
						<?php include WVC_DIR_PATH . 'include/views/form-type-' . $type . '.php'; ?>
					</div>
				</div>
				<div class="wvc-form__loader"></div>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg">
					<filter id="blur" width="110%" height="100%">
						<feGaussianBlur stdDeviation="3" result="blur" />
					</filter>
				</svg>
			</div>

			<?php
			return ob_get_clean();
		}
	}
}
add_shortcode( 'wvc_form', 'wvc_view_form' );