/**
 * Search "Collapse to icon" ([search collapse="true"], per screen).
 *
 * - The bar is moved under the header row the element is in (.header-main /
 *   .header-top / .header-bottom), or its section / column, and opens there
 *   at full width (silver-search-toggle.css).
 * - The toggle (search icon / X) opens and closes it; the field gets focus.
 * - Escape, a click outside, or a screen where the box is not collapsed
 *   (data-collapse) closes it.
 *
 * Registered as a Silver behavior so it also runs on content re-rendered by
 * UX Builder.
 */
(function ($) {
	'use strict';

	if (!window.Silver || typeof window.Silver.behavior !== 'function') {
		return;
	}

	// UX Builder screens, same widths as the CSS.
	var SCREENS = [
		['sm', '(max-width: 549px)'],
		['md', '(min-width: 550px) and (max-width: 849px)'],
		['ml', '(min-width: 850px) and (max-width: 1199px)'],
		['lg', '(min-width: 1200px)']
	];

	function currentScreen() {
		for (var i = 0; i < SCREENS.length; i++) {
			if (window.matchMedia(SCREENS[i][1]).matches) return SCREENS[i][0];
		}
		return 'lg';
	}

	function isCollapsed($wrap) {
		return String($wrap.data('collapse') || '').split(' ').indexOf(currentScreen()) !== -1;
	}

	function anchorOf($wrap) {
		var $anchor = $wrap.closest('.header-main, .header-top, .header-bottom');
		if (!$anchor.length) $anchor = $wrap.closest('.section-content, .section, .col-inner');
		if (!$anchor.length) $anchor = $wrap.parent();
		return $anchor;
	}

	function setOpen($wrap, open) {
		var $bar = $wrap.data('uxSearchBar');
		if (!$bar) return;
		$bar.toggleClass('is-open', open);
		$wrap.children('.ux-search-collapse__toggle').attr('aria-expanded', open ? 'true' : 'false');
		if (open) {
			setTimeout(function () { $bar.find('input.search-field').first().trigger('focus'); }, 30);
		}
	}

	function init(el) {
		var $wrap = $(el);
		if ($wrap.data('uxSearchCollapse')) return;
		$wrap.data('uxSearchCollapse', true);

		var id = $wrap.attr('id');
		var $bar = $wrap.children('.ux-search-collapse__bar');
		var $anchor = anchorOf($wrap);

		// A UX Builder re-render leaves the previous bar in the anchor.
		$anchor.children('.ux-search-collapse__bar').filter(function () {
			return $(this).data('for') === id && this !== $bar[0];
		}).remove();

		if ($anchor.css('position') === 'static') $anchor.css('position', 'relative');
		$anchor.append($bar);
		$wrap.data('uxSearchBar', $bar);

		$wrap.children('.ux-search-collapse__toggle').on('click', function (event) {
			event.preventDefault();
			setOpen($wrap, !$bar.hasClass('is-open'));
		});

		$bar.on('keydown', function (event) {
			if (event.key === 'Escape') {
				setOpen($wrap, false);
				$wrap.children('.ux-search-collapse__toggle').trigger('focus');
			}
		});
	}

	function eachWrap(callback) {
		$('.ux-search-collapse').each(function () {
			var $wrap = $(this);
			if ($wrap.data('uxSearchBar')) callback($wrap, $wrap.data('uxSearchBar'));
		});
	}

	// Click outside the toggle and the bar closes it.
	$(document).on('click', function (event) {
		eachWrap(function ($wrap, $bar) {
			if (!$bar.hasClass('is-open')) return;
			if ($wrap[0].contains(event.target) || $bar[0].contains(event.target)) return;
			setOpen($wrap, false);
		});
	});

	// Screen changes: close the bar where the box is not collapsed.
	function updateAll() {
		eachWrap(function ($wrap, $bar) {
			if ($bar.hasClass('is-open') && !isCollapsed($wrap)) setOpen($wrap, false);
		});
	}
	SCREENS.forEach(function (screen) {
		var query = window.matchMedia(screen[1]);
		if (query.addEventListener) query.addEventListener('change', updateAll);
		else if (query.addListener) query.addListener(updateAll);
	});
	var resizeTimer = null;
	$(window).on('resize orientationchange', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(updateAll, 100);
	});

	window.Silver.behavior('search-collapse', {
		attach: function (context) {
			$(context || document).find('.ux-search-collapse').addBack('.ux-search-collapse').each(function () {
				init(this);
			});
		}
	});
})(jQuery);
