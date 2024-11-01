;(function ($, window, document, undefined) {
	"use strict";

	$('.wvc-form form').on('submit', function() {
		var form = $(this),
			parent = form.closest('.wvc-form'),
			type = form.attr('data-form'),
			message_area = form.find('.wvc-form__response'),
			form_id = parent.attr('data-id'),
			today = new Date(),
			time = today.getDate() + "-" + ( today.getMonth() + 1 ) + "-" + today.getFullYear() + " " + today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();


		if ( type == 'email' || type == 'request-passcode' ) {
			$.ajax({
				type: "POST",
				url: wvc_object.ajax_url,
				dataType: 'json',
				data: {
					action: 'wvc_submit_email',
					email: form.find('input[name=email]').val().trim(),
					form_id: form_id,
					without_key: form.attr('data-close') == 'close',
					user_time: time
				},
				beforeSend: function() {
					parent.addClass('loading');
					message_area.text('');
				},
				success: function(response) {
					message_area.attr('data-type', response.status).text(response.message).addClass('active');
					parent.removeClass('loading');

					if ( response.status == 'success' && form.attr('data-close') == 'close' ) {
						parent.remove();
					}
				}
			});
		}

		if ( type == 'passcode' || type == 'submit-passcode' ) {
			$.ajax({
				type: "POST",
				url: wvc_object.ajax_url,
				dataType: 'json',
				data: {
					action: 'wvc_submit_passcode',
					code: form.find('input[name=pass_code]').val().trim(),
					form_id: form_id,
					user_time: time
				},
				beforeSend: function() {
					parent.addClass('loading');
					message_area.text('');
				},
				success: function(response) {
					// 'status' => 'success',
					if ( response.status != 'success' ) {
						message_area.attr('data-type', response.status).text(response.message).addClass('active');
						parent.removeClass('loading');
					} else {
						parent.remove();
					}
				}
			});
		}

		if ( type == 'type3' ) {
			$.ajax({
				type: "POST",
				url: wvc_object.ajax_url,
				dataType: 'json',
				data: {
					action: 'wvc_submit_type3',
					email: form.find('input[name=email]').val().trim(),
					pass_code: form.find('input[name=pass_code]').val().trim(),
					form_id: form_id,
					user_time: time
				},
				beforeSend: function() {
					parent.addClass('loading');
					message_area.text('');
				},
				success: function(response) {
					if ( response.status != 'success' ) {
						message_area.attr('data-type', response.status).text(response.message).addClass('active');
						parent.removeClass('loading');
					} else {
						parent.remove();
					}
				}
			});
		}

		if ( type == 'full' ) {
			$.ajax({
				type: "POST",
				url: wvc_object.ajax_url,
				dataType: 'json',
				data: {
					action: 'wvc_submit_passcode_full',
					first_name: form.find('input[name=first_name]').val().trim(),
					last_name: form.find('input[name=last_name]').val().trim(),
					email: form.find('input[name=email]').val().trim(),
					form_id: form_id,
					user_time: time
				},
				beforeSend: function() {
					parent.addClass('loading');
					message_area.text('');
				},
				success: function(response) {
					if ( response.status != 'success' ) {
						message_area.attr('data-type', response.status).text(response.message).addClass('active');
						parent.removeClass('loading');
					} else {
						parent.remove();
					}
				}
			});
		}

		return false;
	});




	// Show additional forms
	$('.wvc-form__subform-request-passcode a').on('click', function(e) {
		$(this).closest('.wvc-form__subform-request-passcode').addClass('active');

		e.preventDefault();
	});


	$('.wvc-form__subform-submit-passcode a').on('click', function(e) {
		$(this).closest('.wvc-form__subform-submit-passcode').addClass('active');

		e.preventDefault();
	});

})(jQuery, window, document);