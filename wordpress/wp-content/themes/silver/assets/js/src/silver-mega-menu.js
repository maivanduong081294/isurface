/**
 * Mega menu element ([ux_mega_menu] / [ux_mega_menu_item]).
 *
 * - Desktop (>= 850px): opens on hover or click (data-trigger), hovering a
 *   tab shows its pane (with a short delay so moving the mouse diagonally
 *   to the pane does not switch tabs), clicking a tab with a link opens it.
 * - Mobile: the trigger toggles the panel, tabs toggle their pane (accordion).
 * - Escape / click outside closes; arrow keys move between tabs.
 *
 * Registered as a Silver behavior so it also runs on content re-rendered
 * by UX Builder or loaded with AJAX.
 */
(function ($) {
	'use strict';

	if (!window.Silver || typeof window.Silver.behavior !== 'function') {
		return;
	}

	var DESKTOP = '(min-width: 850px)';
	var HOVER_DELAY = 120;

	function isDesktop() {
		return window.matchMedia(DESKTOP).matches;
	}

	function setOpen($menu, open) {
		if ($menu.hasClass('ux-mega-menu-builder')) {
			open = true; // Always open while editing.
		}
		$menu.toggleClass('is-open', open);
		$menu.children('.ux-mega-menu-trigger').attr('aria-expanded', open ? 'true' : 'false');
	}

	function activate($item) {
		var $items = $item.siblings('.ux-mega-menu-item').addBack();
		$items.removeClass('is-active').children('.ux-mega-menu-tab').attr('aria-expanded', 'false');
		$item.addClass('is-active').children('.ux-mega-menu-tab').attr('aria-expanded', 'true');
	}

	function init(menu) {
		var $menu = $(menu);
		var hoverMode = $menu.data('trigger') === 'hover';
		var openTimer = null;
		var closeTimer = null;
		var tabTimer = null;

		if ($menu.data('uxMegaMenu')) {
			return;
		}
		$menu.data('uxMegaMenu', true);

		// Trigger.
		$menu.children('.ux-mega-menu-trigger').on('click', function (event) {
			event.preventDefault();
			setOpen($menu, !$menu.hasClass('is-open'));
		});

		if (hoverMode) {
			$menu.on('mouseenter', function () {
				if (!isDesktop()) return;
				clearTimeout(closeTimer);
				openTimer = setTimeout(function () { setOpen($menu, true); }, 60);
			}).on('mouseleave', function () {
				if (!isDesktop()) return;
				clearTimeout(openTimer);
				closeTimer = setTimeout(function () { setOpen($menu, false); }, 200);
			});
		}

		// Tabs.
		$menu.on('mouseenter', '.ux-mega-menu-tab', function () {
			if (!isDesktop()) return;
			var $item = $(this).closest('.ux-mega-menu-item');
			clearTimeout(tabTimer);
			tabTimer = setTimeout(function () { activate($item); }, HOVER_DELAY);
		}).on('mouseleave', '.ux-mega-menu-tab', function () {
			clearTimeout(tabTimer);
		});

		$menu.on('click', '.ux-mega-menu-tab', function (event) {
			var $tab = $(this);
			var $item = $tab.closest('.ux-mega-menu-item');
			event.preventDefault();

			if (isDesktop()) {
				// The pane is already shown on hover: a click follows the link.
				if ($item.hasClass('is-active') && $tab.data('href') && !$menu.hasClass('ux-mega-menu-builder')) {
					window.location.href = $tab.data('href');
					return;
				}
				activate($item);
				return;
			}

			// Mobile: accordion.
			if ($item.hasClass('is-active')) {
				$item.removeClass('is-active');
				$tab.attr('aria-expanded', 'false');
			} else {
				activate($item);
			}
		});

		$menu.on('focus', '.ux-mega-menu-tab', function () {
			if (isDesktop()) activate($(this).closest('.ux-mega-menu-item'));
		});

		// Keyboard: arrows between tabs, Escape closes.
		$menu.on('keydown', function (event) {
			if (event.key === 'Escape') {
				setOpen($menu, false);
				$menu.children('.ux-mega-menu-trigger').trigger('focus');
				return;
			}
			if (!$(event.target).hasClass('ux-mega-menu-tab')) return;
			if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;

			var $tabs = $menu.find('.ux-mega-menu-tab');
			var index = $tabs.index(event.target) + (event.key === 'ArrowDown' ? 1 : -1);
			event.preventDefault();
			$tabs.eq((index + $tabs.length) % $tabs.length).trigger('focus');
		});
	}

	// Click outside closes every open menu.
	$(document).on('click', function (event) {
		$('.ux-mega-menu.is-open').each(function () {
			if (!this.contains(event.target)) setOpen($(this), false);
		});
	});

	window.Silver.behavior('mega-menu', {
		attach: function (context) {
			// The context can be the menu itself (UX Builder re-render).
			$(context || document).find('.ux-mega-menu').addBack('.ux-mega-menu').each(function () {
				init(this);
			});
		}
	});
})(jQuery);
