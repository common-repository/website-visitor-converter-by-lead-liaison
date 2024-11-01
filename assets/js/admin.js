;(function ($, window, document, undefined) {
	"use strict";

	function getFormData($form){
	    var unindexed_array = $form.serializeArray();
	    var indexed_array = {};

	    $.map(unindexed_array, function(n, i){
	        indexed_array[n['name']] = n['value'];
	    });

	    return indexed_array;
	}

	$('.wvc-expire-date').datepicker({
		dateFormat : 'd MM, yy'
	});


	// Generate new codes
	$('.wvc-generate-codes form').on('submit', function(){
		var form_data = getFormData($(this)),
			form_id = $(this).find('select[name=form_id]'),
			container = $('.wvc-codes-list table tbody');

		form_id.removeClass('not-valid');
		if (  form_id.val() == '0' ) {
			form_id.addClass('not-valid');
			return false;
		}

		$.ajax({
			type: "POST",
			url: window.ajaxurl,
			data: ({
				action: 'wvc_generate_codes',
				form_data: form_data
			}),
			success: function( response ) {
				if ( container.length ) {
					var html = '',
						data = JSON.parse(response);
						
					for( var i in data ) {
						html = html + '<tr class="wvc-codes-list__new" data-code="' + data[i]['code'] + '">' +
								'<td>' + data[i]['code'] + '</td>' +
								'<td>' + data[i]['valid_to'] + '</td>' +
								'<td>' + data[i]['form_id'] + '</td>' +
								'<td>' + data[i]['status'] + '</td>' +
								'<td>' + data[i]['limitation'] + '</td>' +
								'<td>' +
									'<a href="#" class="edit">Edit</a> | ' +
									'<a href="#" class="delete">Delete</a>' +
								'</td>' +
							'</tr>';
					}

					container.prepend(html);
				} else {
					document.location.reload(true);
				}
			}
		});

		return false;
	});


	// Delete code
	$('.wvc-codes-list').on('click', '.delete', function(e) {
		var row = $(this).closest('tr'),
			code = row.attr('data-code');

		$.ajax({
			type: "POST",
			url: window.ajaxurl,
			data: ({
				action: 'wvc_delete_code',
				code: code
			}),
			success: function( response ) {
				if ( response == 1 ) {
					row.remove();
				}
			}
		});
		
		e.preventDefault();
	});


	// Edit code
	$('.wvc-codes-list').on('click', '.edit', function(e) {
		var row = $(this).closest('tr'),
			code = row.attr('data-code');

		$.ajax({
			type: "POST",
			url: window.ajaxurl,
			dataType: 'json',
			data: ({
				action: 'wvc_get_one',
				code: code
			}),
			success: function( response ) {
				var expire = response.valid_to != '0' ? response.valid_to : '',
					form_id = response.form_id,
					limitation = response.limitation,
					status = response.status;


				$('.wvc-codes-list tr').removeClass('wvc-codes-list__new');

				$('.wvc-edit-code input[name="limitation"]').val('');
				if ( Number.isInteger( limitation * 1 ) ) {
					$('.wvc-edit-code input[name="limitation"]').val(limitation);
				}

				$('.wvc-edit-code input[name="code"]').val(code);

				$('.wvc-edit-code input[name="expire"]').datepicker({
					dateFormat : 'd MM, yy'
				});

				if ( expire.length ) {
					$('.wvc-edit-code input[name="expire"]').datepicker("setDate", new Date(expire) );
				} else {
					$('.wvc-edit-code input[name="expire"]').val('');
				}

				$('.wvc-edit-code select[name="status"] option[value=' + status + ']').prop({selected: true});
				$('.wvc-edit-code select[name="form_id"] option[value=' + form_id + ']').prop({selected: true});

				$('.wvc-edit-code').addClass('active');

				$('html, body').animate({
			        scrollTop: $('.wvc-edit-code').offset().top - 50
			    }, 400);

			}
		});

		e.preventDefault();
	});

	$('.wvc-edit-code button.cancel').on('click', function() {
		var code = $('.wvc-edit-code input[name="code"]').val(),
			row = $('.wvc-codes-list').find('tr[data-code="' + code + '"]');

		$('.wvc-edit-code').removeClass('active');

		row.addClass('wvc-codes-list__new');

		$('html, body').animate({
	        scrollTop: row.offset().top - 50
	    }, 400);
	});

	$('.wvc-edit-code form').on('submit', function(){
		var form_data = getFormData($(this)),
			container = $('.wvc-codes-list table tbody'),
			code = $('.wvc-edit-code input[name="code"]').val(),
			row = $('.wvc-codes-list').find('tr[data-code="' + code + '"]');

		$.ajax({
			type: "POST",
			url: window.ajaxurl,
			data: ({
				action: 'wvc_update_code',
				form_data: form_data
			}),
			success: function( response ) {
				if ( response != 'error' ) {
					var html = '',
						data = JSON.parse(response);
						
						html = '<td>' + code + '</td>' +
								'<td>' + data['valid_to'] + '</td>' +
								'<td>' + data['form_id'] + '</td>' +
								'<td>' + data['status'] + '</td>' +
								'<td>' + data['limitation'] + '</td>' +
								'<td>' +
									'<a href="#" class="edit">Edit</a> | ' +
									'<a href="#" class="delete">Delete</a>' +
								'</td>';

					row.html(html);

					$('.wvc-edit-code').removeClass('active');

					row.addClass('wvc-codes-list__new');

					$('html, body').animate({
				        scrollTop: row.offset().top - 50
				    }, 400);
				}
			}
		});

		return false;
	});



	function wvc_type_ff( value ) {
		$('.js-wvc-options-block__wrap input, .js-wvc-options-block__wrap textarea').hide();

		$('.wvc-options-block__wrap .wvc-ft' + value ).show();
	}

	$('.wvc-options-block__wrap input[name="type"]').on('change', function(){
		wvc_type_ff($(this).val());
	});

	$(window).on('load', function(){
		wvc_type_ff($('.wvc-options-block__wrap input:checked').val());
	});


	$('.wvc-color-picker').wpColorPicker();
	

	// Add code
	$('.js-add-code').on('click', function(e) {
		$('.wvc-add-code').toggleClass('active');
		e.preventDefault();
	});

	$('.wvc-add-code button.cancel').on('click', function() {
		$('.wvc-add-code').removeClass('active');
		$(this).closest('form')[0].clear(true);
		return false;
	});

	$('.wvc-add-code form').on('submit', function(){
		var $form = $(this),
			form_data = getFormData($form),
			$msg = $form.find('.wvc-add-code__msg'),
			container = $('.wvc-codes-list table tbody');

		$msg.text(''); 
		$.ajax({
			type: "POST",
			url: window.ajaxurl,
			dataType: 'json',
			data: ({
				action: 'wvc_add_code',
				form_data: form_data
			}),
			success: function( response ) {
				if ( response.type != 'error' ) {
					var html = '',
						data = response.data;
						
						html = '<tr class="wvc-codes-list__new" data-code="' + data.code + '">' + 
								'<td>' + data.code + '</td>' +
								'<td>' + data['valid_to'] + '</td>' +
								'<td>' + data['form_id'] + '</td>' +
								'<td>' + data['status'] + '</td>' +
								'<td>' + data['limitation'] + '</td>' +
								'<td>' +
									'<a href="#" class="edit">Edit</a> | ' +
									'<a href="#" class="delete">Delete</a>' +
								'</td>' +
							'</td>';

					container.prepend(html);

					$('.wvc-add-code').removeClass('active');
					$form.trigger('reset');
				} else {
					$msg.text(response.data); 
				}
			}
		});

		return false;
	});

})(jQuery, window, document);