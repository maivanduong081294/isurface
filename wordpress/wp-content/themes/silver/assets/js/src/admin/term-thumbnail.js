/**
 * Term thumbnail field (inc/admin/backend/term-thumbnail.php): pick / remove
 * an image from the media library.
 */
jQuery(function ($) {
	'use strict';

	var l10n = window.silverTermThumbnail || {};

	function reset($field) {
		$field.find('.silver-term-thumbnail-id').val('');
		$field.find('.silver-term-thumbnail-preview').empty();
		$field.find('.silver-term-thumbnail-remove').hide();
	}

	$(document).on('click', '.silver-term-thumbnail-upload', function (event) {
		event.preventDefault();

		var $field = $(this).closest('.silver-term-thumbnail-field');
		var frame = wp.media({
			title: l10n.title,
			button: { text: l10n.button },
			library: { type: 'image' },
			multiple: false
		});

		frame.on('select', function () {
			var image = frame.state().get('selection').first().toJSON();
			var url = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;

			$field.find('.silver-term-thumbnail-id').val(image.id);
			$field.find('.silver-term-thumbnail-preview').empty().append($('<img>', { src: url, alt: '' }));
			$field.find('.silver-term-thumbnail-remove').show();
		});

		frame.open();
	});

	$(document).on('click', '.silver-term-thumbnail-remove', function (event) {
		event.preventDefault();
		reset($(this).closest('.silver-term-thumbnail-field'));
	});

	// "Add new term" is saved with AJAX: clear the field afterwards, like the
	// other fields of the form.
	$(document).ajaxComplete(function (event, request, options) {
		if (!options || typeof options.data !== 'string' || options.data.indexOf('action=add-tag') === -1) return;
		if (request && request.responseXML && $(request.responseXML).find('wp_error').length) return;
		reset($('#addtag .silver-term-thumbnail-field'));
	});
});
