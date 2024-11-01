<?php

if ( ! function_exists( 'wvc_post_exists' ) ) {
	function wvc_post_exists( $id ) {
		return is_string( get_post_status( $id ) );	
	}
}

if ( ! function_exists( 'wvc_admin_enqueue_scripts' ) ) {
	function wvc_admin_enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'jquery-ui', WVC_DIR_URL . '/assets/css/jquery-ui.css' );

		wp_enqueue_script( 'wvc-admin-script', WVC_DIR_URL . '/assets/js/admin.js', array('jquery', 'wp-color-picker'), false, true );
		wp_enqueue_style( 'wvc-admin-styles', WVC_DIR_URL . '/assets/css/admin.css' );
	}
}
add_action( 'admin_enqueue_scripts', 'wvc_admin_enqueue_scripts' );


/**
 * Include google fonts.
 *
 * @return string
 */
if ( ! function_exists( 'wvc_fonts_url' ) ) {
	function wvc_fonts_url() {
		$fonts = array(
			'Roboto:wght@400;500',
		);

		$font_url = '';
		/*
		Translators: If there are characters in your language that are not supported
		by chosen font(s), translate this to 'off'. Do not translate into your own language.
		 */
		if ( 'off' !== _x( 'on', 'Google font: on or off', 'gruffygoat' ) ) {
			$font_url = add_query_arg( 'family', ( implode( '|', $fonts ) . "&display=swap" ), "//fonts.googleapis.com/css2" );
		}
		return $font_url;
	}
}


if ( ! function_exists( 'wvc_enqueue_scripts' ) ) {
	function wvc_enqueue_scripts() {
		if ( is_admin() ) return false;
		
		wp_enqueue_style( 'wvc-fonts', wvc_fonts_url() );
		wp_enqueue_style( 'oblurlay', WVC_DIR_URL . '/assets/css/oblurlay.min.css' );
		wp_enqueue_style( 'wvc-styles', WVC_DIR_URL . '/assets/css/style.css' );
		
		wp_enqueue_script( 'wvc-script', WVC_DIR_URL . '/assets/js/scripts.js', array('jquery'), false, true );

		wp_localize_script( 'wvc-script', 'wvc_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'site_url' => site_url('/'),
			'template_url' => get_template_directory_uri(),
			'loading' => esc_html__('Loading...', 'wvc-forms')
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'wvc_enqueue_scripts' );


if ( ! function_exists( 'wwvc_set_content_type' ) ) {
	function wwvc_set_content_type(){
		return "text/html";
	}
}


if ( ! function_exists( 'wvc_save_email' ) ) {
	function wvc_save_email( $data ) {
		global $wpdb;

		$subject = 'New WVC Submission';

		$message = 'You have a new Website Visitor Converter (WVC) submission:';
		$message .= '<ul>';
		$message .= '<li>Email: ' . $data['email'] . '</li>';
		$message .= '<li>Name: ' . $data['name'] . '</li>';
		$message .= '</ul>';

		add_filter( 'wp_mail_content_type','wwvc_set_content_type' );

		wp_mail( get_option('admin_email'), $subject, $message );
		
		remove_filter( 'wp_mail_content_type','wwvc_set_content_type' );

		return $wpdb->insert( $wpdb->prefix . 'wvc_emails', $data );
	}
}

if ( ! function_exists( 'wvc_send_post' ) ) {
	function wvc_send_post( $url, $data ) {
		$response = wp_remote_post( $url, array(
		        'method' => 'POST',
		        'httpversion' => '1.0',
		        'sslverify' => false,
		        'body' => json_encode( $data )
	    	)
	    );

		if ( is_wp_error( $response ) ) {
			if( wp_remote_retrieve_response_code( $response ) === 200 ) {
				return wp_remote_retrieve_body( $response );		
			}
		}
	}
}

if ( ! function_exists( 'wvc_check_keys' ) ) {
	function wvc_check_keys( $options ) {
		$datavalidation_key = $options['datavalidation_key'];
		$workflow_id = $options['workflow_id'];
		$leadliaison_key = $options['leadliaison_key'];

		if ( empty( $leadliaison_key ) ) {
			$respond = array(
				'status'  => 'error',
				'message' => 'Please set Lead Liaison API key'
			);

			return $respond;
		}

		return array( 'status'  => 'success' );
	}
}


if ( ! function_exists( 'wvc_validate_email' ) ) {
	function wvc_validate_email( $email, $options ) {

		$args = array(
		    'headers'     => array(
		    	'Content-Type' => 'application/json',
		        'Authorization' => 'bearer ' . $options['datavalidation_key'],
		    ),
		); 
		
		$remote_url = 'https://dv3.datavalidation.com/api/v2/realtime/?email=' . $email;
		$response = wp_remote_get( $remote_url, $args );

		if( wp_remote_retrieve_response_code( $response ) === 200 ) {
			$body = wp_remote_retrieve_body( $response );

			$json = json_decode($result, true);

			if ( $json['status'] != 'ok' ) {
				$respond = array(
					'status' => 'error',
					'message' => esc_html__( 'Some error happened, please try again later', 'wvc-forms' )
				);

				return $respond;
			}

			if ( $json['grade'] == 'F' ) {
				$respond = array(
					'status' => 'error',
					'message' => esc_html__( 'Please enter a valid email address', 'wvc-forms' )
				);

				return $respond;
			}

			if ( $json['free_email'] ) {
				$respond = array(
					'status'  => 'error',
					'message' => esc_html__( 'Personal email addresses are not accepted. Please use a business email address', 'wvc-forms' )
				);

				return $respond;
			}

			return array( 'status' => 'success' );
		}
	}
}


if ( ! function_exists( 'wvc_add_to_workflow' ) ) {
	function wvc_add_to_workflow($args) {

		if ( ! empty( $args['workflow_id'] ) ) {
			$url = 'https://api.leadliaison.com/v1.0/prospects/add_to_workflow.json?api_key=' . $args['leadliaison_key'];
			$data = array(
				'prospect_id' => $args['prospect_id'],
				'workflow_id' => $args['workflow_id']
			);

			$add_to_workflow = wvc_send_post($url, $data);
			$add_to_workflow_data = json_decode( $add_to_workflow, true );

			if ( $add_to_workflow_data['status'] != 200 ) {
				$respond = array(
					'status' => 'error',
					'message' => esc_html__( 'Some error happened, please try again later', 'wvc-forms' )
				);
				
				return $respond;
			}
		}

		return array( 'status' => 'success' );
	}
}


if ( ! function_exists( 'wvc_submit_email' ) ) {
	function wvc_submit_email() {
		if ( ! empty( $_POST['email'] ) ) {
			$options = get_option( 'wvc_plugin' );
			$form_id = sanitize_title( $_POST['form_id'] );
			$automation_id = wvc_post_exists( $form_id ) && get_post_meta( $form_id, 'automation_id', true ) ? get_post_meta( $form_id, 'automation_id', true ) : '';
			$without_key = isset( $_POST['without_key'] ) && $_POST['without_key'] == true;

			$options['workflow_id'] = ! empty( $automation_id ) ? $automation_id : $options['workflow_id'];

			if ( ! $without_key ) {
				$result = wvc_check_keys( $options );
				if ( $result['status'] == 'error' ) {
					echo json_encode( $result );
					wp_die();
				}
			}

			$workflow_id 	 = $options['workflow_id'];
			$leadliaison_key = $options['leadliaison_key'];

			$email  = sanitize_email( $_POST['email'] );

			if ( ! empty( $options['datavalidation_key'] ) ) {
				$result = wvc_validate_email( $email, $options );

				if ( $result['status'] == 'error') {
					echo json_encode( $result );
					wp_die();
				}
			}

			if ( ! empty( $leadliaison_key ) ) {
			
				$url  = 'https://api.leadliaison.com/v1.0/prospects/upsert.json?api_key=' . $leadliaison_key;

				$data = array(
					'Email' => $email,
					'is_merge_multipicklist' => 1
				);

				$upsert = wvc_send_post($url, $data);
				$upsert_data = json_decode( $upsert, true );
				if ( $upsert_data['status'] != 200 ) {
					$respond = array(
						'status' => 'error',
						'message' => esc_html__( 'Some error happened, please try again later', 'wvc-forms' ), 
					);
					echo json_encode( $respond );
					wp_die();
				}

				$result = wvc_add_to_workflow( array(
					'leadliaison_key' => $leadliaison_key,
					'prospect_id' 	  => $upsert_data['prospect_id'],
					'workflow_id' 	  => $workflow_id,
				) );

				if ( $result['status'] == 'error' ) {
					echo json_encode( $result );
					wp_die();
				}
			}

			$user_data = array(
				'date' => ! empty( $_POST['user_time'] ) ? strtotime( $_POST['user_time'] ) : time(),
				'name' => '',
				'email' => $email,
				'form_id' => $form_id
			);

			wvc_save_email( $user_data );
			
			$respond = array(
				'status' => 'success',
				'message' => esc_html__( 'Request sent! Please check your email for your Pass Code.', 'wvc-forms' )
			);

			echo json_encode( $respond );
			wp_die();

		} else {
			$respond = array(
				'status' => 'error',
				'message' => esc_html__( 'Email is required', 'wvc-forms' )
			);

			echo json_encode( $respond );
			wp_die();
		}
	}
}
add_action('wp_ajax_wvc_submit_email', 'wvc_submit_email');
add_action('wp_ajax_nopriv_wvc_submit_email', 'wvc_submit_email');

if ( ! function_exists( 'wvc_use_passcode' ) ) {
	function wvc_use_passcode( $code = '', $form_id ) {

		if ( ! empty( $code ) ) {
			$result = (new WVC_Codes)->use_code( $code, $form_id );

			switch ( $result ) {
				case 'expired':
					$respond = array(
						'status' => 'error',
						'message' => esc_html__( 'Expired code', 'wvc-forms' )
					);
					break;
				case 'wrong-code':
					$respond = array(
						'status' => 'error',
						'message' => esc_html__( 'Invalid code', 'wvc-forms' )
					);
					break;
				case 'limit-reached':
					$respond = array(
						'status' => 'error',
						'message' => esc_html__( 'Pass Code limit reached', 'wvc-forms' )
					);
					break;
				case 'success':
					$respond = array(
						'status' => 'success',
						'message' => esc_html__( 'Request sent! Please check your email for your Pass Code.', 'wvc-forms' )
					);
					break;
			}

		} else {
			$respond = array(
				'status' => 'error',
				'message' => esc_html__( 'Pass code field required', 'wvc-forms' )
			);
		}

		return $respond;
	}
}


if ( ! function_exists( 'wvc_submit_passcode' ) ) {
	function wvc_submit_passcode() {
		echo json_encode( wvc_use_passcode( (int)$_POST['code'], (int)$_POST['form_id'] ) );
		wp_die();
	}
}
add_action('wp_ajax_wvc_submit_passcode', 'wvc_submit_passcode');
add_action('wp_ajax_nopriv_wvc_submit_passcode', 'wvc_submit_passcode');



if ( ! function_exists( 'wvc_submit_passcode_full' ) ) {
	function wvc_submit_passcode_full() {
		if ( ! empty( $_POST['email'] ) && ! empty( $_POST['last_name'] ) && ! empty( $_POST['first_name'] ) ) {

			$options = get_option( 'wvc_plugin' );
			$form_id = sanitize_title( $_POST['form_id'] );
			$automation_id = wvc_post_exists( $form_id ) && get_post_meta( $form_id, 'automation_id', true ) ? get_post_meta( $form_id, 'automation_id', true ) : '';

			$options['workflow_id'] = ! empty( $automation_id ) ? $automation_id : $options['workflow_id'];

			$result = wvc_check_keys( $options );
			if ( $result['status'] == 'error') {
				echo json_encode( $result );
				wp_die();
			}

			$workflow_id 	 = $options['workflow_id'];
			$leadliaison_key = $options['leadliaison_key'];

			$email  	= sanitize_email( $_POST['email'] );
			$first_name = sanitize_title( $_POST['first_name'] );
			$last_name  = sanitize_title( $_POST['last_name'] );

			if ( ! empty( $options['datavalidation_key'] ) ) {
				$result = wvc_validate_email( $email, $options );

				if ( $result['status'] == 'error') {
					echo json_encode( $result );
					wp_die();
				}
			}

			
			$url  = 'https://api.leadliaison.com/v1.0/prospects/add.json?api_key=' . $leadliaison_key;

			$data = array(
				'Email' => $email,
				'FirstName' => $first_name,
				'LastName' => $last_name
			);

			$upsert = wvc_send_post($url, $data);
			$upsert_data = json_decode( $upsert, true );
			if ( $upsert_data['status'] != 200 ) {
				$respond = array(
					'status' => 'error',
					'message' => esc_html__( 'Some error happened, please try again later', 'wvc-forms' )
				);
				echo json_encode( $respond );
				wp_die();
			}

			$result = wvc_add_to_workflow( array(
				'leadliaison_key' => $leadliaison_key,
				'prospect_id' 	  => $upsert_data['prospect_id'],
				'workflow_id' 	  => $workflow_id,
			) );

			if ( $result['status'] == 'error' ) {
				echo json_encode( $result );
				wp_die();
			}
			
			$respond = array(
				'status' => 'success',
				'message' => esc_html__( 'Request sent! Please check your email for your Pass Code.', 'wvc-forms' )
			);

			$user_data = array(
				'date' => ! empty( $_POST['user_time'] ) ? strtotime( $_POST['user_time'] ) : time(),
				'name' => $first_name . ' ' . $last_name,
				'email' => $email,
				'form_id' => $form_id
			);
			
			wvc_save_email( $user_data );

			echo json_encode( $respond );
			wp_die();
		} else {
			$respond = array(
				'status' => 'error',
				'message' => esc_html__( 'All fields are required', 'wvc-forms' )
			);

			echo json_encode( $respond );
			wp_die();
		}
	}
}
add_action('wp_ajax_wvc_submit_passcode_full', 'wvc_submit_passcode_full');
add_action('wp_ajax_nopriv_wvc_submit_passcode_full', 'wvc_submit_passcode_full');


if ( ! function_exists( 'wvc_submit_type3' ) ) {
	function wvc_submit_type3() {
		if ( ! empty( $_POST['email'] ) && ! empty( $_POST['pass_code'] ) ) {
			$options = get_option( 'wvc_plugin' );
			$automation_id = wvc_post_exists( $_POST['form_id'] ) && get_post_meta( $_POST['form_id'], 'automation_id', true ) ? get_post_meta( $_POST['form_id'], 'automation_id', true ) : '';

			$options['workflow_id'] = ! empty( $automation_id ) ? $automation_id : $options['workflow_id'];

			$result = wvc_check_keys( $options );
			if ( $result['status'] == 'error') {
				echo json_encode( $result );
				wp_die();
			}

			$workflow_id 	 = $options['workflow_id'];
			$leadliaison_key = $options['leadliaison_key'];

			$email  	= sanitize_email( $_POST['email'] );
			$pass_code  = sanitize_title( $_POST['pass_code'] );
			$form_id    = sanitize_title( $_POST['form_id'] );


			$result = wvc_use_passcode( $pass_code, $form_id );
			if ( $result['status'] == 'error' ) {
				echo json_encode( $result );
				wp_die();
			}


			if ( ! empty( $options['datavalidation_key'] ) ) {
				$result = wvc_validate_email( $email, $options );
				if ( $result['status'] == 'error' ) {
					echo json_encode( $result );
					wp_die();
				}
			}

			$url  = 'https://api.leadliaison.com/v1.0/prospects/upsert.json?api_key=' . $leadliaison_key;
			$data = array(
				'Email' => $email,
				'is_merge_multipicklist' => 1
			);

			$upsert = wvc_send_post($url, $data);
			$upsert_data = json_decode( $upsert, true );
			if ( $upsert_data['status'] != 200 ) {
				$respond = array(
					'status' => 'error',
					'message' => esc_html__( 'Some error happened, please try again later', 'wvc-forms' )
				);
				echo json_encode( $respond );
				wp_die();
			}

			$result = wvc_add_to_workflow( array(
				'leadliaison_key' => $leadliaison_key,
				'prospect_id' 	  => $upsert_data['prospect_id'],
				'workflow_id' 	  => $workflow_id,
			) );

			if ( $result['status'] == 'error' ) {
				echo json_encode( $result );
				wp_die();
			}

			$user_data = array(
				'date' => ! empty( $_POST['user_time'] ) ? strtotime( $_POST['user_time'] ) : time(),
				'name' => '',
				'email' => $email,
				'form_id' => $form_id
			);
			wvc_save_email( $user_data );

			echo json_encode( $result );
			wp_die();

		} else {
			$respond = array(
				'status' => 'error',
				'message' => esc_html__( 'All fields are required', 'wvc-forms' )
			);

			echo json_encode( $respond );
			wp_die();
		}
	}
}
add_action('wp_ajax_wvc_submit_type3', 'wvc_submit_type3');
add_action('wp_ajax_nopriv_wvc_submit_type3', 'wvc_submit_type3');

