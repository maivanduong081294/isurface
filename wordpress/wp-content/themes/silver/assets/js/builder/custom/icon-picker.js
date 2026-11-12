/**
 * Font Awesome icon picker option type for UX Builder.
 *
 * Registers the "icon" option type template and the <ux-option-icon>
 * component. Clicking the field opens a grid popup with search and style
 * tabs. Icon data comes from window.silverIconPicker (see icon-picker.php).
 */
(function (angular, window, document) {
	'use strict';

	if (!angular || !window.silverIconPicker) {
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

	function getPopup() {
		if (!popup) popup = new Popup();
		return popup;
	}

	/* ------------------------------------------------------------------ */
	/* Angular option type                                                  */
	/* ------------------------------------------------------------------ */

	var app = angular.module('uxBuilder');

	app.run(['$templateCache', function ($templateCache) {
		$templateCache.put(
			'components/ux-option/types/icon.html',
			'<ux-option-icon option="$ctrl.option" model="$ctrl.model"></ux-option-icon>'
		);
	}]);

	function IconOptionController($timeout) {
		this.$timeout = $timeout;
	}

	IconOptionController.$inject = ['$timeout'];

	IconOptionController.prototype.iconClass = function () {
		return iconClass(this.model);
	};

	IconOptionController.prototype.label = function () {
		return iconLabel(this.model);
	};

	IconOptionController.prototype.open = function () {
		var self = this;

		getPopup().open(this.model, function (value) {
			// Apply inside a digest so the builder picks up the change.
			self.$timeout(function () {
				self.model = value || self.option.default || '';
			});
		});
	};

	IconOptionController.prototype.clear = function () {
		this.model = this.option.default || '';
	};

	app.component('uxOptionIcon', {
		controller: IconOptionController,
		bindings: { option: '<', model: '=' },
		template:
			'<div class="silver-icon-field" ng-class="{ \'has-icon\': $ctrl.model }">' +
				'<button type="button" class="silver-icon-field__select" ng-click="$ctrl.open()" title="Choose icon">' +
					'<span class="silver-icon-field__preview">' +
						'<i ng-if="$ctrl.model" ng-class="$ctrl.iconClass()" aria-hidden="true"></i>' +
						'<span ng-if="!$ctrl.model" class="dashicons dashicons-plus-alt2"></span>' +
					'</span>' +
					'<span class="silver-icon-field__label">{{ $ctrl.label() }}</span>' +
				'</button>' +
				'<button type="button" class="silver-icon-field__clear" ng-if="$ctrl.model" ng-click="$ctrl.clear()" title="Remove icon">' +
					'<span class="dashicons dashicons-no-alt"></span>' +
				'</button>' +
			'</div>'
	});
})(window.angular, window, document);
