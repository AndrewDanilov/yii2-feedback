$(function () {

	$.fancybox.defaults.afterClose = function() {
		var form = this.$content.find('form');
		form.siblings('.w-form-fail').hide();
		form.siblings('.w-form-done').hide();
		form.trigger("reset");
	};

	//$.fancybox.defaults.btnTpl.smallBtn =
	//	'<a href="javascript:$.fancybox.close();" class="close-btn">x</a>';

	// обрабатываются только формы с атрибутом data-feedback
	$('form[data-feedback]').on('submit', function(e) {

		var delay = 4000; // задержка исчезновения лайтбокса в миллисекундах (0 - не скрывать)
		var wait_msg = 'Отправка...'; // сообщение об отправке (оставить пустым чтоб не показывать)
		var redirect = ''; // страница, на котороую перейти после отправки (оставить пустым чтоб никуда не переходить)
		var action = '/feedback'; // путь к скрипту отправки почты по умолчанию

		e.preventDefault();
		var form = $(this);

		// лайтбокс форма
		var form_lightbox = form.filter('[data-lightbox]').length;

		var cur_redirect = form.attr('data-redirect');
		if (cur_redirect) {
			redirect = cur_redirect;
		}

		var cur_action = form.attr('action');
		if (cur_action) {
			action = cur_action;
		}

		var submit_div = form.find('[type="submit"]');
		if (submit_div.hasClass('btn-disabled')) {
			// if form in process - exiting
			return;
		}
		submit_div.addClass('btn-disabled');

		var submit_txt = submit_div.attr('value');
		if (wait_msg !== '') {
			submit_div.attr('value', wait_msg);
		}

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
			submit_div.attr('value', submit_txt);
			submit_div.removeClass('btn-disabled');

			var success = result && result.success;

			if (success) {
				if (!redirect) {
					form.siblings('.w-form-fail').hide();
					form.siblings('.w-form-done').show();
					if (form_lightbox) {
						setTimeout(function () {
							$.fancybox.close();
						}, delay);
					} else {
						form.hide();
					}
				}
				if (result.callback && typeof window[result.callback] === 'function') {
					var callback = window[result.callback];
					callback(form);
				}
				if (redirect) {
					document.location.href = redirect;
					return true;
				}
			} else {
				form.siblings('.w-form-fail').show();
				if (form_lightbox) {
					setTimeout(function () {
						form.siblings('.w-form-fail').hide();
					}, delay);
				}
			}

			// reset ReCaptcha
			if (typeof(grecaptcha) !== 'undefined') {
				grecaptcha.reset();
			}
		});
	});
});