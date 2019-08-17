var andrewdanilovFeedback = {
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
								$.fancybox.close();
							}, delay);
						} else {
							form.hide();
						}
					}
					// triggering submit event
					$(document).trigger(form.id + '-submit');
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
		if (typeof errors === 'object') {
			for (var field in errors) {
				if (errors.hasOwnProperty(field)) {
					form.find('.field-data-' + field)
						.addClass('has-error')
						.find('.help-block')
						.text(errors[field][0]);
				}
			}
		}
	},
	hideErrors: function (form) {
		form.find('.has-error')
			.removeClass('has-error')
			.find('.help-block')
			.text('');
	}
};

$(function () {
	$.fancybox.defaults.afterClose = function() {
		var form = this.$content.find('form');
		andrewdanilovFeedback.hideErrors();
		andrewdanilovFeedback.hideSuccess();
		form.trigger("reset");
	};
});