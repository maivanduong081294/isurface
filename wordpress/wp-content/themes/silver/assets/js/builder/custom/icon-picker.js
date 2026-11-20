/**
 * Font Awesome icon picker option type for UX Builder.
 *
 * Registers the "icon" option type template and the <ux-option-icon>
 * component. Clicking the field opens the shared grid popup
 * (icon-picker-popup.js, also used by the Customizer icon control).
 */
(function (angular, window) {
	'use strict';

	var picker = window.silverIconPickerPopup;

	if (!angular || !picker) {
		return;
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
		return picker.iconClass(this.model);
	};

	IconOptionController.prototype.label = function () {
		return picker.iconLabel(this.model);
	};

	IconOptionController.prototype.open = function () {
		var self = this;

		picker.open(this.model, function (value) {
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
})(window.angular, window);
