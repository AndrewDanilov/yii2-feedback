var andrewdanilovFeedback = {
	error_field_class: 'has-error',
	error_field_alert_element_class: 'help-block',
	register: function (form_id, redirect, is_lightbox, delay) {
		var self = andrewdanilovFeedback;
		// catch events only on registered forms
		$('form#' + form_id).on('submit', function(e) {
			e.preventDefault();
			var form = $(this);

			var action = form.attr('action');

			var submit_btn = form.find('[type="submit"]');
			if (submit_btn.hasClass('btn-disabled')) {
				// if form in process - leaving
				return;
			}
			submit_btn.addClass('btn-disabled');

			var formData = new FormData(form[0]);
			var param = $('meta[name=csrf-param]').attr("content");
			var token = $('meta[name=csrf-token]').attr("content");
			if (param && token) {
				formData.set(param, token);
			}
			$.ajax({
				url: action,
				type: 'POST',
				dataType: 'json',
				processData: false,
				contentType: false,
				data: formData
			}).done(function(result) {
				submit_btn.removeClass('btn-disabled');

				if (result && result.success) {
					if (!redirect) {
						self.showSuccess(form);
						if (is_lightbox) {
							setTimeout(function () {
								$('[data-fancybox-close]').click();
								$.fancybox.close(true);
							}, delay);
						} else {
							form.hide();
						}
					}
					// triggering submit event
					$(document).trigger(form.attr('id') + '-submit');
					// redirecting if it needs
					if (redirect) {
						document.location.href = redirect;
						return true;
					}
				} else {
					self.showErrors(form, result['errors']);
				}

				// reset ReCaptcha
				if (typeof(grecaptcha) !== 'undefined') {
					grecaptcha.reset();
				}
			});
		}).find('[name^="data["]').focus(function () {
			// hide field error on focus
			andrewdanilovFeedback.hideError($(this));
		});
	},
	showSuccess: function (form) {
		andrewdanilovFeedback.hideErrors(form);
		form.siblings('.form-success').show();
	},
	hideSuccess: function (form) {
		form.siblings('.form-success').hide();
	},
	showErrors: function (form, errors) {
		andrewdanilovFeedback.hideErrors(form);
		if (typeof errors === 'object') {
			if (Object.keys(errors).length) {
				for (var field in errors) {
					if (errors.hasOwnProperty(field)) {
						form.find('[name="data[' + field + ']"]')
							.addClass(andrewdanilovFeedback.error_field_class)
							.parent()
							.addClass(andrewdanilovFeedback.error_field_class)
							.find('.' + andrewdanilovFeedback.error_field_alert_element_class)
							.text(errors[field][0]);
					}
				}
			} else {
				alert('При отправке сообщения произошла неожиданная ошибка. Попробуйте позже.');
			}
		}
	},
	hideErrors: function (form) {
		form.find('.' + andrewdanilovFeedback.error_field_class)
			.removeClass(andrewdanilovFeedback.error_field_class)
			.find('.' + andrewdanilovFeedback.error_field_alert_element_class)
			.text('');
	},
	hideError: function (field) {
		field.removeClass(andrewdanilovFeedback.error_field_class)
			.parent()
			.removeClass(andrewdanilovFeedback.error_field_class)
			.find('.' + andrewdanilovFeedback.error_field_alert_element_class)
			.text('');
	}
};

$(function () {
	$.fancybox.defaults.afterClose = function() {
		var form = this.$content.find('form');
		andrewdanilovFeedback.hideErrors(form);
		andrewdanilovFeedback.hideSuccess(form);
		form.trigger("reset");
		form.find('[name="data[extra]"]').remove();
	};
});

$(function () {
	$('[data-fancybox][data-src][data-extra]').click(function() {
		var extra = $(this).attr('data-extra');
		var widget = $(this).attr('data-src');

		var form = $(widget + ' form');

		var extra_data_el;

		extra_data_el = form.find('input[name="data[extra]"]');
		if (extra_data_el.length) {
			extra_data_el.remove();
		}
		$('<input />', {
			'type': 'hidden',
			'name': 'data[extra]',
			'value': extra,
			appendTo: form
		});
	});
});