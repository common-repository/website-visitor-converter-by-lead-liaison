<?php

if ( ! function_exists( 'wvc_register_post_type' ) ) {
	function wvc_register_post_type() {
		$labels = array(
			'name' => _x('WVC Forms', 'plural'),
			'singular_name' => _x('WVC Form', 'singular'),
			'menu_name' => _x('WVC Forms', 'admin menu'),
			'name_admin_bar' => _x('WVC Forms', 'admin bar'),
			'add_new' => _x('Add New WVC Form', 'add new'),
			'add_new_item' => __('Add New WVC Forms', 'wvc-forms'),
			'new_item' => __('New WVC Forms', 'wvc-forms'),
			'edit_item' => __('Edit WVC Form', 'wvc-forms'),
			'view_item' => __('View WVC Form', 'wvc-forms'),
			'all_items' => __('WVC Forms', 'wvc-forms'),
			'search_items' => __('Search WVC Forms', 'wvc-forms'),
			'not_found' => __('No WVC Forms found.', 'wvc-forms'),
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'query_var' => true,
			'publicly_queryable'  => false,
			'has_archive' => true,
			'hierarchical' => false,
			'supports' => array('title')
		);

		register_post_type('wvc-forms', $args);
	}
}
add_action('init', 'wvc_register_post_type');