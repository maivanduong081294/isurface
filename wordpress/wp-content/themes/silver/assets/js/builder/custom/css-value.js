(function (angular, window) {
  'use strict';
  if (!angular) return;
  var app = angular.module('uxBuilder');
  function length(value) {
    value = value == null ? '' : String(value).trim();
    return /^(?:\d+(?:\.\d*)?|\.\d+)$/.test(value) ? value + 'rem' : value;
  }
  app.filter('silverCssValue', function () { return length; });
  app.run(['$templateCache', function (cache) {
    cache.put('components/ux-option/types/css-value.html',
      '<ux-option-css-value option="$ctrl.option" model="$ctrl.model"></ux-option-css-value>');
  }]);
  app.component('uxOptionCssValue', {
    bindings: { option: '<', model: '=' },
    controller: function () {
      this.$onChanges = function () { this.draft = this.model; };
      this.$doCheck = function () {
        if (this.previous !== this.model) {
          this.draft = this.model;
          this.previous = this.model;
          this.invalid = false;
        }
      };
      this.change = function () {
        var value = String(this.draft || '').trim();
        var property = this.option.css_property || 'gap';
        this.invalid = !!value && (property === 'order'
          ? !/^[+-]?\d+$/.test(value)
          : /[;{}<>\\]/.test(value) || !window.CSS.supports(property, length(value)));
        if (!this.invalid) this.model = value;
      };
      this.reset = function () {
        this.model = this.option.default == null ? '' : String(this.option.default);
        this.draft = this.model;
        this.invalid = false;
      };
    },
    template: '<div style="display:flex;gap:6px;align-items:center">' +
      '<input type="text" style="min-width:0;flex:1;width:0" ng-model="$ctrl.draft" ng-change="$ctrl.change()" ng-attr-aria-label="{{$ctrl.option.heading}}" ng-attr-aria-invalid="{{$ctrl.invalid}}" placeholder="{{$ctrl.option.placeholder}}">' +
      '<button type="button" ng-click="$ctrl.reset()" title="Reset to default for this screen" aria-label="Reset to default for this screen" style="min-height:32px">Reset</button></div>' +
      '<small ng-if="$ctrl.invalid" role="alert">Enter a valid CSS value (Order requires an integer).</small>'
  });

  // Options already resolve to the selected preview breakpoint in Builder.
  app.run(['$rootScope', function ($rootScope) {
    if (!window.UxBuilder) return;
    var watches = new WeakMap();
    function applyOrder(shortcode) {
      if (!shortcode || !shortcode.$element || !shortcode.options) return;
      var value = shortcode.options.order;
      var elements = shortcode.$element.filter(function () {
        return this.nodeType === 1 && !/^(STYLE|SCRIPT)$/.test(this.tagName);
      });
      elements.css('order', /^[+-]?\d+$/.test(String(value)) ? value : '');
    }
    window.UxBuilder.on('shortcode-attached', function (shortcode) {
      if (!shortcode || watches.has(shortcode)) return;
      watches.set(shortcode, $rootScope.$watch(function () {
        return shortcode.options && shortcode.options.order;
      }, function () { applyOrder(shortcode); }));
    });
    window.UxBuilder.on('shortcode-detached', function (shortcode) {
      if (!shortcode || !watches.has(shortcode)) return;
      watches.get(shortcode)();
      watches.delete(shortcode);
    });
    ['shortcode-attached', 'shortcode-changed', 'shortcode-recompiled'].forEach(function (event) {
      window.UxBuilder.on(event, applyOrder);
    });
  }]);
})(window.angular, window);
