/**
 * "Quick view" action for the UX Builder toolbar.
 *
 * Toggles a clean preview of the layout being edited: the sidebar is
 * collapsed and every editing tool inside the preview iframe (outlines,
 * handles, add buttons, placeholders) is hidden. Nothing is saved or
 * reloaded, so the preview always shows the current, unsaved layout.
 * The breakpoint buttons stay available to check each screen size.
 */
(function (window, document) {
	'use strict';

	if (!window.UxBuilder || typeof window.UxBuilder.addAction !== 'function') {
		return;
	}

	var CLASS_NAME = 'uxb-quick-view';
	var active = false;
	var sidebarWasVisible = true;
	var services = null;
	var boundIframe = null;

	function iframeDocument() {
		var iframe = services.$iframe();
		return iframe && iframe.length ? iframe.get(0).contentDocument : null;
	}

	// The class lives on <html> of both documents; quick-view.css does the rest.
	function applyClass() {
		document.documentElement.classList.toggle(CLASS_NAME, active);

		var doc = iframeDocument();
		if (doc && doc.documentElement) {
			doc.documentElement.classList.toggle(CLASS_NAME, active);
			// Escape must work while the preview has focus too.
			doc.removeEventListener('keydown', onKeydown);
			doc.addEventListener('keydown', onKeydown);
		}
	}

	// The preview iframe can reload (e.g. after a save): keep the state in sync.
	function bindIframe() {
		var iframe = services.$iframe();
		var element = iframe && iframe.length ? iframe.get(0) : null;

		if (!element || element === boundIframe) return;

		boundIframe = element;
		element.addEventListener('load', applyClass);
	}

	function setActive(value) {
		var store = services.store;

		if (value === active) return;
		active = value;

		if (active) {
			sidebarWasVisible = store.showSidebar !== false;
			store.showSidebar = false;
		} else {
			store.showSidebar = sidebarWasVisible;
		}

		bindIframe();
		applyClass();
	}

	function onKeydown(event) {
		if (!active || event.key !== 'Escape') return;

		// Runs outside of an Angular digest.
		services.$timeout(function () {
			setActive(false);
		});
	}

	window.UxBuilder.addAction({
		icon: 'dashicons dashicons-visibility',
		tooltip: 'Quick view',
		handler: function ($injector) {
			if (!services) {
				services = {
					store: $injector.get('store'),
					$iframe: $injector.get('$iframe'),
					$timeout: $injector.get('$timeout')
				};
				document.addEventListener('keydown', onKeydown);
			}

			setActive(!active);
		}
	});
})(window, document);
