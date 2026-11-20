/**
 * Customizer icon control (inc/admin/customizer/customizer-icon-control.php).
 *
 * The field shows the chosen icon and its name; clicking it opens the
 * Font Awesome picker of UX Builder (icon-picker-popup.js), the cross button
 * clears it. The setting holds the icon classes, or "" for none.
 */
(function (api) {
	'use strict';

	if (!api || !window.silverIconPickerPopup) {
		return;
	}

	var picker = window.silverIconPickerPopup;

	api.controlConstructor['silver-icon'] = api.Control.extend({
		ready: function () {
			var control = this;
			var container = control.container[0];
			var field = container.querySelector('.silver-icon-field');
			var preview = container.querySelector('.silver-icon-field__preview');
			var label = container.querySelector('.silver-icon-field__label');

			function render(value) {
				var icon = document.createElement(value ? 'i' : 'span');

				if (value) {
					icon.className = picker.iconClass(value);
					icon.setAttribute('aria-hidden', 'true');
				} else {
					icon.className = 'dashicons dashicons-plus-alt2';
				}

				preview.replaceChildren(icon);
				label.textContent = value ? picker.iconLabel(value) : control.params.emptyLabel;
				field.classList.toggle('has-icon', !!value);
			}

			container.querySelector('.silver-icon-field__select').addEventListener('click', function () {
				picker.open(control.setting.get(), function (value) {
					control.setting.set(value || '');
				});
			});

			container.querySelector('.silver-icon-field__clear').addEventListener('click', function () {
				control.setting.set('');
			});

			control.setting.bind(render);
			render(control.setting.get());
		}
	});
})(window.wp && window.wp.customize);
