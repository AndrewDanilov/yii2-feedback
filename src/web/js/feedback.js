var andrewdanilovFeedback = {
	forms: [],
	register: function (form_id, redirect, is_lightbox, delay) {
		andrewdanilovFeedback.forms.push({
			id: form_id,
			redirect: redirect,
			is_lightbox: is_lightbox,
			delay: delay,
		});
	}
};

$(function () {

	$.fancybox.defaults.afterClose = function() {
		var form = this.$content.find('form');
		form.siblings('.form-fail').hide();
		form.siblings('.form-done').hide();
		form.trigger("reset");
	};

	// catch events only on forms registered in andrewdanilovFeedback obj
	andrewdanilovFeedback.forms.forEach(function (form_options) {
		$('form#' + form_options.id).on('submit', function(e) {
			e.preventDefault();
			var form = $(this);

			var is_lightbox = form_options.is_lightbox;
			var redirect = form_options.redirect;
			var action = form.attr('action');

			var submit_btn = form.find('[type="submit"]');
			if (submit_btn.hasClass('btn-disabled')) {
				// if form in process - exiting
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
						form.siblings('.form-fail').hide();
						form.siblings('.form-done').show();
						if (is_lightbox) {
							setTimeout(function () {
								$.fancybox.close();
							}, form_options.delay);
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
					form.siblings('.form-fail').show();
					if (is_lightbox) {
						setTimeout(function () {
							form.siblings('.form-fail').hide();
						}, form_options.delay);
					}
				}

				// reset ReCaptcha
				if (typeof(grecaptcha) !== 'undefined') {
					grecaptcha.reset();
				}
			});
		});
	});
});