jQuery(document).ready(function ($) {
	'use strict';

	var $set_button = $('#dfi-set-dfi'),
		$td = $set_button.parent(),
		$hidden_input = $td.find('#dfi_id'),
		$del_button = $td.find('#dfi-no-fdi');

	/**
	 * @param html the preview html
	 */
	function set_preview_html(html) {
		var $cur_preview = $td.find('#preview-image');
		// remove old
		$cur_preview.remove();
		// prepend new
		$td.prepend(html);
		//disable button
		$del_button.removeClass('button-disabled');
	}

	/**
	 * @param image_id int
	 * @return html string with the image
	 */
	function set_preview_image(image_id) {
		var data = {
				action: 'dfi_change_preview',
				image_id: image_id
			};

		$.post(ajaxurl, data, function (response) {
			set_preview_html(response);
		});

		// return responseText;
	}

	/**
	 * set a loading image until the ajax is ready
	 */
	function set_loading_image() {
		var $cur_preview = $td.find('#preview-image'),
			html = '<div id="preview-image" style="float:left; padding: 0 5px 0 0; height: 60px;"><img src="images/loading.gif"/></div>';

		$cur_preview.remove();
		$td.prepend(html);
	}

	/**
	 * @param selected_id the selected image id
	 */
	function set_dfi(selected_id) {
		$hidden_input.val(selected_id);
		// set preview
		set_loading_image();
		set_preview_image(selected_id);
	}

	// remove featured image
	$del_button.click(function (e) {
		e.preventDefault();
		var $cur_preview = $td.find('#preview-image');
		$cur_preview.remove();
		$hidden_input.val('');
		$(this).addClass('button-disabled');
	});

	/**
	 * open the media manager
	 */
	$set_button.click(function (e) {
		e.preventDefault();
		var frame = wp.media({
			title : dfi_L10n.manager_title,
			multiple : false,
			library : { type : 'image' },
			button : { text : dfi_L10n.manager_button }
		});
		// close event media manager
		frame.on('close', function () {
			var images = frame.state().get('selection');
			// set the images
			images.each(function (image) {
				set_dfi(image.id);
			});
		});

		// open event media manager
		frame.on('open', function () {
			var attachment,
				selection = frame.state().get('selection'),
				id = $hidden_input.val();

			attachment = wp.media.attachment(id);
			attachment.fetch();

			selection.add(attachment ? [ attachment ] : []);
		});

		// everything is set open the media manager
		frame.open();
	});


}); // doc rdy
