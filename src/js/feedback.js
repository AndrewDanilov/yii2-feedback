$(function () {

	$.fancybox.defaults.afterClose = function() {
		var form = this.$content.find('form');
		form.siblings('.form-fail').hide();
		form.siblings('.form-done').hide();
		form.trigger("reset");
	};

	//$.fancybox.defaults.btnTpl.smallBtn =
	//	'<a href="javascript:$.fancybox.close();" class="close-btn">x</a>';

	// catch only forms with data-feedback-form attr
	$('form[data-feedback-form]').on('submit', function(e) {

		e.preventDefault();
		var form = $(this);

		var is_lightbox = form.parents('[data-lightbox]').length;
		var redirect = form.attr('data-redirect');
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
						}, 4000);
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
					}, 4000);
				}
			}

			// reset ReCaptcha
			if (typeof(grecaptcha) !== 'undefined') {
				grecaptcha.reset();
			}
		});
	});
});