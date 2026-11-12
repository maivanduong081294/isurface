/**
 * Theme colors in the UX Builder color options.
 *
 * A color option can hold a CSS variable of the theme, for example
 * var(--fs-color-primary), so the element follows the theme options. The color
 * picker only understands real colors: given a variable it shows black and, as
 * soon as it is opened and closed, writes that black back into the option.
 *
 * This extends the color picker so that it
 * - shows the color the variable stands for right now, and
 * - keeps the variable until the user really picks another color.
 *
 * The variables are defined on the editor document by
 * inc/builder/theme-colors.php.
 */
(function (angular, window, document) {
	'use strict';

	if (!angular) {
		return;
	}

	var VARIABLE = /^var\(\s*(--[\w-]+)\s*\)$/;

	function isVariable(value) {
		return typeof value === 'string' && VARIABLE.test(value.trim());
	}

	// The color a theme variable stands for; any other value is returned as is.
	function resolve(value) {
		if (!isVariable(value)) {
			return value;
		}

		var name = VARIABLE.exec(value.trim())[1];
		var color = window.getComputedStyle(document.documentElement).getPropertyValue(name).trim();

		return color || value;
	}

	angular.module('uxBuilder').config(['$provide', function ($provide) {
		$provide.decorator('uxOptionColorpickerDirective', ['$delegate', function ($delegate) {
			var directive = $delegate[0];
			var Base = directive.controller;

			function ThemeColorpicker(app, $element, $scope) {
				var controller = Reflect.construct(Base, [app, $element, $scope], ThemeColorpicker);
				var $input = controller.$colorpicker;
				var spectrum = $input.spectrum;

				// What the picker shows while the option holds a variable.
				controller.shownColor = null;

				// Hand the picker the resolved color instead of the variable.
				$input.spectrum = function (method, value) {
					var args = Array.prototype.slice.call(arguments);
					var setsColor = false;

					if (method === 'set') {
						args[1] = resolve(value);
						setsColor = true;
					} else if (method && typeof method === 'object') {
						args[0] = angular.extend({}, method, { color: resolve(method.color) });
						setsColor = true;
					}

					var result = spectrum.apply(this, args);

					if (setsColor) {
						var shown = spectrum.call(this, 'get');
						controller.shownColor = shown ? shown.toString() : null;
					}

					return result;
				};

				return controller;
			}

			ThemeColorpicker.prototype = Object.create(Base.prototype);
			ThemeColorpicker.prototype.constructor = ThemeColorpicker;

			// The picker reports its color when it moves and when it closes.
			// Closing it untouched reports the resolved color of the variable:
			// keep the variable in that case.
			ThemeColorpicker.prototype.setColor = function (color) {
				if (color && isVariable(this.model) && color.toString() === this.shownColor) {
					return;
				}

				Base.prototype.setColor.call(this, color);
			};

			ThemeColorpicker.$inject = Base.$inject;
			directive.controller = ThemeColorpicker;

			return $delegate;
		}]);
	}]);
})(window.angular, window, document);
