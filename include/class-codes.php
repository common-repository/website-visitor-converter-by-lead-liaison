<?php

if ( ! class_exists( 'WVC_Codes' ) ) {
	class WVC_Codes {
		public $db, $wp_table;
		public $db_table = 'wvc_codes';

		public function __construct() {
			global $wpdb;
			$this->db = $wpdb;

			$this->wp_table = $wpdb->prefix . $this->db_table;
		}

		public function init() {
			add_action( 'wp_ajax_wvc_generate_codes', array( $this, 'generate_codes' ) );
			add_action( 'wp_ajax_wvc_delete_code', array( $this, 'delete' ) );
			add_action( 'wp_ajax_wvc_update_code', array( $this, 'update' ) );
			add_action( 'wp_ajax_wvc_add_code', array( $this, 'add' ) );
			add_action( 'wp_ajax_wvc_get_one', array( $this, 'get_one' ) );
		}

		public function get_row( $code ) {
			return $this->db->get_row( $this->db->prepare( "SELECT * FROM $this->wp_table WHERE code = %d;", $code ), ARRAY_A );
		}

		public function get_one() {
			if ( ! empty( $_POST['code'] ) ) {
				$code = (int)$_POST['code'];

				echo json_encode( $this->get_row( $code ) );
			}

			die();
		}

		public function get_list() {
			return $this->db->get_results("SELECT * from $this->wp_table order by id desc;", ARRAY_A );
		}

		public function delete() {
			if ( isset( $_POST['code'] ) && is_numeric( $_POST['code'] ) ) {
				echo json_encode( $this->db->delete( $this->wp_table, array( 'code' => $_POST['code'] ) ) );
			}

			die();
		}

		public function save( $data ) {
			return $this->db->insert( $this->wp_table, $data );
		}

		public function update( $data ) {
			if ( ! empty( $_POST['form_data'] ) ) {
				$code 	 = intval( $_POST['form_data']['code'] );
				$form_id = intval( $_POST['form_data']['form_id'] );
				$status  = sanitize_text_field( $_POST['form_data']['status'] );
				$limitation = ! empty( $_POST['form_data']['limitation'] ) ? intval( $_POST['form_data']['limitation'] ) : '';
				$expire  = ! empty( $_POST['form_data']['expire'] ) ? intval( strtotime( $_POST['form_data']['expire'] ) ) : '';

				$result = $this->db->update( 
					$this->wp_table, 
					array(
						'status' => $status,
						'form_id' => $form_id,
						'limitation' => $limitation,
						'valid_to' => $expire
					), 
					array( 'code' => $code ) 
				);

				if ( $result ) {
					$status_cell = $status == 'yes' ? esc_html__( 'Not used', 'wvc-forms' ) : esc_html__( 'Used', 'wvc-forms' );
					
					if ( $limitation != '' ) {
						$row = $this->get_row( $code );
						$status_cell = $row['used_times'] . '/' . $limitation;
					}

					echo json_encode( array(
						'code' => $code,
						'limitation' => $limitation == '' ? esc_html__( 'No limit', 'wvc-forms' ) : $limitation,
						'form_id' => '<a href="' . get_edit_post_link( $form_id ) . '" data-id="' . esc_attr( $form_id ) . '">
										' . get_the_title( $form_id ) . '
									</a>',
						'status' => $status_cell,
						'valid_to' => is_numeric( $expire ) && $expire > 0 ? date('F j, Y', $expire) : ''
					) );
				} else {
					esc_html_e( 'error', 'wvc-forms' );
				}
			}

			die();	
		}

		public function add() {
			$result = array();
			if ( ! empty( $_POST['form_data'] ) ) {
				$code 	 = intval( $_POST['form_data']['code'] );
				$form_id = intval( $_POST['form_data']['form_id'] );
				$status  = esc_html( $_POST['form_data']['status'] );
				$limitation = ! empty( $_POST['form_data']['limitation'] ) ? intval( $_POST['form_data']['limitation'] ) : '';
				$expire  = ! empty( $_POST['form_data']['expire'] ) ? intval( strtotime( $_POST['form_data']['expire'] ) ) : '';

				// Check if pass code is unique
				if ( ! $this->code_exist( $code ) ) {
					if ( $form_id != 0 ) {
						$data = array(
							'code' => $code,
							'status' => 'yes',
							'form_id' => $form_id,
							'limitation' => $limitation,
							'used_times' => 0,
							'valid_to' => $expire
						);

						if ( $this->save( $data ) ) {
							$status_cell = esc_html__( 'Not used', 'wvc-forms' );
							if ( $limitation != '' ) {
								$status_cell = '0/' . $limitation;
							}

							$result_data = array(
								'code' => $code,
								'form_id' => '<a href="' . get_edit_post_link( $form_id ) . '" data-id="' . esc_attr( $form_id ) . '">
												' . get_the_title( $form_id ) . '
											</a>',
								'form_title' => get_the_title( $form_id ),
								'used_times' => 0,
								'limitation' => $limitation == '' ? esc_html__( 'No limit', 'wvc-forms' ) : $limitation,
								'status' => $status_cell,
								'valid_to' => is_numeric( $expire ) && $expire > 0 ? date('F j, Y', $expire) : ''
							);

							$result = array(
								'type' => 'success',
								'data' => $result_data
							);
						}
					} else {
						$result = array(
							'type' => 'error',
							'data' => esc_html__( 'Please choose the form', 'wvc-forms' )
						);
					}
				} else {
					$result = array(
						'type' => 'error',
						'data' => esc_html__( 'Pass Code already exist, please use unique Code', 'wvc-forms' )
					);
				}
			}

			echo json_encode($result);
			die();
		}

		public function use_code($code, $form_id) {
			$today = time();

			$results = $this->db->get_results( $this->db->prepare( "SELECT * from $this->wp_table where code = '%d' and form_id = %d;", array( $code, $form_id ) ) );

			if ( count( $results ) == 1 ) {
				if ( ! empty( $results[0]->valid_to ) && $results[0]->valid_to < $today ) {
					return 'expired';
				} else {
					$used_times = $results[0]->used_times;
					$limitation = $results[0]->limitation;
					if ( ! empty( $limitation ) && $used_times != $limitation ) {
						$used_times++;
					} else {
						if ( $used_times == $limitation ) {
							return 'limit-reached';
						}
					}

					$this->db->update( 
						$this->wp_table, 
						array(
							'status' => 'no',
							'used_times' => $used_times,
						), 
						array( 
							'id' => $results[0]->id,
							'code' => $code
						) 
					);

					return 'success';
				}
			} else {
				return 'wrong-code';
			}

			return $code_id;
		}

		private function code_exist( $code ) {
			return $this->db->get_var( $this->db->prepare( "SELECT count(*) from $this->wp_table where code = %d;", $code ) );
		}

		public function generate_codes() {
			$result = array();
			if ( ! empty( $_POST ) ) {
				$count	 = intval( $_POST['form_data']['count'] );
				$form_id = intval( $_POST['form_data']['form_id'] );
				$limitation = ! empty( $_POST['form_data']['limitation'] ) ? intval( $_POST['form_data']['limitation'] ) : '';
				$expire  = ! empty( $_POST['form_data']['expire'] ) ? intval( strtotime( $_POST['form_data']['expire'] ) ) : '';

				for( $i = 0; $i < $count; $i++ ) {
					$code = mt_rand ( 10000, 99999 );

					if ( ! $this->code_exist( $code ) ) {
						$data = array(
							'code' => $code,
							'status' => 'yes',
							'form_id' => $form_id,
							'limitation' => $limitation,
							'used_times' => 0,
							'valid_to' => $expire
						);

						if ( $this->save( $data ) ) {
							$status_cell = esc_html__( 'Not used', 'wvc-forms' );
							if ( $limitation != '' ) {
								$status_cell = '0/' . $limitation;
							}

							$result[] = array(
								'code' => $code,
								'form_id' => '<a href="' . get_edit_post_link( $form_id ) . '" data-id="' . esc_attr( $form_id ) . '">
												' . get_the_title( $form_id ) . '
											</a>',
								'form_title' => get_the_title( $form_id ),
								'used_times' => 0,
								'limitation' => $limitation == '' ? esc_html__( 'No limit', 'wvc-forms' ) : $limitation,
								'status' => $status_cell,
								'valid_to' => is_numeric( $expire ) && $expire > 0 ? date('F j, Y', $expire) : ''
							);
						}
					}
				}
			}

			echo json_encode( $result );
			die();
		}
	}
}
(new WVC_Codes)->init();

