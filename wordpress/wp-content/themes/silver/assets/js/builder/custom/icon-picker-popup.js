/**
 * Font Awesome icon picker popup, shared by UX Builder and the Customizer.
 *
 * A grid popup with search and style tabs (All / Solid / Regular / Brands).
 * Icon data comes from window.silverIconPicker (see inc/builder/icon-picker.php).
 *
 * Usage:
 *   window.silverIconPickerPopup.open( currentValue, function ( value ) { ... } );
 * value is the icon classes ("fa-solid fa-chevron-right"), or "" for "Remove icon".
 */
(function (window, document) {
	'use strict';

	if (!window.silverIconPicker) {
		return;
	}

	var data = window.silverIconPicker;
	var legacy = data.legacy || {};
	var byValue = {};

	var icons = (data.icons || []).map(function (row) {
		var name = row[0].replace(/fa-(solid|regular|brands)\s+fa-/, '');
		var icon = {
			value: row[0],
			label: row[1],
			style: row[2],
			search: (row[1] + ' ' + name + ' ' + (row[3] || '')).toLowerCase()
		};
		byValue[icon.value] = icon;
		return icon;
	});

	var STYLES = [
		{ key: '', label: 'All' },
		{ key: 'solid', label: 'Solid' },
		{ key: 'regular', label: 'Regular' },
		{ key: 'brands', label: 'Brands' }
	];

	function escapeHtml(value) {
		return String(value).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function iconClass(value) {
		if (!value) return '';
		return legacy[value] || value;
	}

	function iconLabel(value) {
		if (!value) return 'None';
		if (byValue[value]) return byValue[value].label;
		return value.replace(/^icon-/, '');
	}

	/* ------------------------------------------------------------------ */
	/* Popup (one instance shared by all icon fields)                       */
	/* ------------------------------------------------------------------ */

	function Popup() {
		var self = this;

		this.value = '';
		this.style = '';
		this.query = '';
		this.onSelect = null;
		this.renderTimer = null;

		this.el = document.createElement('div');
		this.el.className = 'silver-icon-picker';
		this.el.innerHTML =
			'<div class="silver-icon-picker__backdrop"></div>' +
			'<div class="silver-icon-picker__dialog" role="dialog" aria-label="Select icon">' +
				'<div class="silver-icon-picker__header">' +
					'<div class="silver-icon-picker__search">' +
						'<input type="text" placeholder="Filter by name..." autocomplete="off" spellcheck="false">' +
						'<span class="dashicons dashicons-search"></span>' +
					'</div>' +
					'<button type="button" class="silver-icon-picker__close" title="Close">' +
						'<span class="dashicons dashicons-no-alt"></span>' +
					'</button>' +
				'</div>' +
				'<div class="silver-icon-picker__tabs"></div>' +
				'<div class="silver-icon-picker__body">' +
					'<div class="silver-icon-picker__grid"></div>' +
					'<p class="silver-icon-picker__empty" hidden>No icons match your search.</p>' +
				'</div>' +
				'<div class="silver-icon-picker__footer">' +
					'<span class="silver-icon-picker__count"></span>' +
					'<button type="button" class="silver-icon-picker__none">Remove icon</button>' +
				'</div>' +
			'</div>';

		this.$backdrop = this.el.querySelector('.silver-icon-picker__backdrop');
		this.$input = this.el.querySelector('.silver-icon-picker__search input');
		this.$tabs = this.el.querySelector('.silver-icon-picker__tabs');
		this.$grid = this.el.querySelector('.silver-icon-picker__grid');
		this.$empty = this.el.querySelector('.silver-icon-picker__empty');
		this.$count = this.el.querySelector('.silver-icon-picker__count');

		this.$tabs.innerHTML = STYLES.map(function (style) {
			return '<button type="button" data-style="' + style.key + '">' + style.label + '</button>';
		}).join('');

		this.$backdrop.addEventListener('click', function () { self.close(); });
		this.el.querySelector('.silver-icon-picker__close').addEventListener('click', function () { self.close(); });
		this.el.querySelector('.silver-icon-picker__none').addEventListener('click', function () { self.select(''); });

		this.$tabs.addEventListener('click', function (event) {
			var button = event.target.closest('button[data-style]');
			if (!button) return;
			self.style = button.getAttribute('data-style');
			self.updateTabs();
			self.render();
		});

		this.$grid.addEventListener('click', function (event) {
			var item = event.target.closest('[data-value]');
			if (!item) return;
			self.select(item.getAttribute('data-value'));
		});

		this.$input.addEventListener('input', function () {
			self.query = self.$input.value.trim().toLowerCase();
			clearTimeout(self.renderTimer);
			self.renderTimer = setTimeout(function () { self.render(); }, 120);
		});

		this.$input.addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				var first = self.$grid.querySelector('[data-value]');
				if (first) self.select(first.getAttribute('data-value'));
				event.preventDefault();
			}
		});

		this.onKeydown = function (event) {
			if (event.key === 'Escape') {
				self.close();
				event.stopPropagation();
			}
		};

		document.body.appendChild(this.el);
	}

	Popup.prototype.open = function (value, onSelect) {
		this.value = legacy[value] || value || '';
		this.onSelect = onSelect;
		this.query = '';
		this.$input.value = '';

		// Start on the tab of the current icon so it is visible immediately.
		this.style = byValue[this.value] ? '' : this.style;

		this.updateTabs();
		this.render();
		this.el.classList.add('is-open');
		document.addEventListener('keydown', this.onKeydown, true);

		var self = this;
		window.requestAnimationFrame(function () {
			self.$input.focus();
			var active = self.$grid.querySelector('.is-active');
			if (active && active.scrollIntoView) {
				active.scrollIntoView({ block: 'center' });
			}
		});
	};

	Popup.prototype.close = function () {
		this.el.classList.remove('is-open');
		this.onSelect = null;
		document.removeEventListener('keydown', this.onKeydown, true);
	};

	Popup.prototype.select = function (value) {
		var callback = this.onSelect;
		this.close();
		if (callback) callback(value);
	};

	Popup.prototype.updateTabs = function () {
		var self = this;
		Array.prototype.forEach.call(this.$tabs.children, function (button) {
			button.classList.toggle('is-active', button.getAttribute('data-style') === self.style);
		});
	};

	Popup.prototype.filtered = function () {
		var style = this.style;
		var query = this.query;

		return icons.filter(function (icon) {
			if (style && icon.style !== style) return false;
			if (query && icon.search.indexOf(query) === -1) return false;
			return true;
		});
	};

	Popup.prototype.render = function () {
		var list = this.filtered();
		var current = this.value;
		var html = '';

		for (var i = 0; i < list.length; i++) {
			var icon = list[i];
			var label = escapeHtml(icon.label);
			html +=
				'<button type="button" class="silver-icon-picker__item' + (icon.value === current ? ' is-active' : '') + '"' +
					' data-value="' + escapeHtml(icon.value) + '" title="' + label + ' (' + escapeHtml(icon.value) + ')">' +
					'<i class="' + escapeHtml(icon.value) + '" aria-hidden="true"></i>' +
					'<span>' + label + '</span>' +
				'</button>';
		}

		this.$grid.innerHTML = html;
		this.$grid.scrollTop = 0;
		this.$empty.hidden = list.length > 0;
		this.$count.textContent = list.length + ' / ' + icons.length + ' icons';
	};

	var popup = null;

	window.silverIconPickerPopup = {
		/**
		 * Opens the picker on the current value; onSelect gets the chosen
		 * icon classes, or "" when "Remove icon" is clicked.
		 */
		open: function (value, onSelect) {
			if (!popup) popup = new Popup();
			popup.open(value, onSelect);
		},
		iconClass: iconClass,
		iconLabel: iconLabel
	};
})(window, document);
