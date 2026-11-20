/**
 * Mega menu element ([ux_mega_menu] / [ux_mega_menu_item]).
 *
 * - Desktop (>= 850px): opens on hover or click (data-trigger), hovering a
 *   tab shows its pane (with a short delay so moving the mouse diagonally
 *   to the pane does not switch tabs), clicking a tab with a link opens it.
 * - Mobile: the trigger toggles the panel, tabs toggle their pane (accordion).
 * - Display "Icon + drawer" (per screen, data-drawer): .is-drawer, the
 *   panel slides in from the side and works as the accordion on any screen.
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

	// UX Builder screens, same widths as the CSS (sm < 550 < md < 850 < ml < 1200 <= lg).
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

	// Display "Icon + drawer" on the screens listed in data-drawer.
	function updateDrawer($menu) {
		var screens = String($menu.data('drawer') || '').split(' ');
		var drawer = screens.indexOf(currentScreen()) !== -1;
		if (drawer === $menu.hasClass('is-drawer')) return;
		if ($menu.hasClass('is-open') && !$menu.hasClass('ux-mega-menu-builder')) setOpen($menu, false);
		$menu.toggleClass('is-drawer', drawer);
	}

	// Desktop behaviour (hover, tabs | panes) only when it is not a drawer.
	function desktopMode($menu) {
		return isDesktop() && !$menu.hasClass('is-drawer');
	}

	function setOpen($menu, open) {
		if ($menu.hasClass('ux-mega-menu-builder')) {
			open = true; // Always open while editing.
		}
		$menu.toggleClass('is-open', open);
		$menu.children('.ux-mega-menu-trigger').attr('aria-expanded', open ? 'true' : 'false');

		// Drawer: no page scroll behind it.
		if (!$menu.hasClass('ux-mega-menu-builder')) {
			$('html').toggleClass('ux-mega-menu-locked', $('.ux-mega-menu.is-drawer.is-open').not('.ux-mega-menu-builder').length > 0);
		}

		if (open) placePanel($menu);
	}

	var EDGE = 16; // Minimum space between the panel and the screen edges.

	/**
	 * Desktop panel position (not the drawer, not UX Builder). The panel stays
	 * inside the container the menu is in (header / row) and inside the screen:
	 * - never wider than that space (Width is a maximum);
	 * - Align "Container": starts at the container's left edge and takes its
	 *   width, like the design;
	 * - other alignments: shifted back inside when they overflow.
	 */
	function placePanel($menu) {
		var panel = $menu.children('.ux-mega-menu-panel')[0];
		if (!panel) return;

		panel.style.removeProperty('--ux-mega-shift');
		panel.style.removeProperty('--ux-mega-left');
		panel.style.removeProperty('--ux-mega-fit-width');

		if (!desktopMode($menu) || $menu.hasClass('ux-mega-menu-builder')) return;

		var menuRect = $menu[0].getBoundingClientRect();
		var viewport = document.documentElement.clientWidth;

		// Space available: the container (inner box), within the screen.
		var minX = EDGE;
		var maxX = viewport - EDGE;
		var container = $menu.closest('.container, .header-inner, .row, .section-content')[0];
		if (container) {
			var rect = container.getBoundingClientRect();
			var style = window.getComputedStyle(container);
			minX = Math.max(minX, rect.left + parseFloat(style.paddingLeft || 0));
			maxX = Math.min(maxX, rect.right - parseFloat(style.paddingRight || 0));
		}
		var space = Math.max(0, maxX - minX);

		if ($menu.hasClass('ux-mega-menu-align-container')) {
			panel.style.setProperty('--ux-mega-left', Math.round(minX - menuRect.left) + 'px');
			panel.style.setProperty('--ux-mega-fit-width', Math.round(space) + 'px');
			return;
		}

		if (panel.getBoundingClientRect().width > space) {
			panel.style.setProperty('--ux-mega-fit-width', Math.round(space) + 'px');
		}

		var panelRect = panel.getBoundingClientRect();
		var shift = 0;
		if (panelRect.right > maxX) shift = maxX - panelRect.right;
		if (panelRect.left + shift < minX) shift = minX - panelRect.left;
		if (shift) panel.style.setProperty('--ux-mega-shift', Math.round(shift) + 'px');
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

		if ($menu.is('[data-drawer]')) updateDrawer($menu);

		// Trigger.
		$menu.children('.ux-mega-menu-trigger').on('click', function (event) {
			event.preventDefault();
			setOpen($menu, !$menu.hasClass('is-open'));
		});

		// Mobile drawer: close button, and a click on the dimmed page (the
		// overlay is the menu's ::after, so the event target is the menu).
		$menu.on('click', '.ux-mega-menu-close', function (event) {
			event.preventDefault();
			setOpen($menu, false);
			$menu.children('.ux-mega-menu-trigger').trigger('focus');
		});
		$menu.on('click', function (event) {
			if (event.target === menu && $menu.hasClass('is-open')) setOpen($menu, false);
		});

		if (hoverMode) {
			$menu.on('mouseenter', function () {
				if (!desktopMode($menu)) return;
				clearTimeout(closeTimer);
				openTimer = setTimeout(function () { setOpen($menu, true); }, 60);
			}).on('mouseleave', function () {
				if (!desktopMode($menu)) return;
				clearTimeout(openTimer);
				closeTimer = setTimeout(function () { setOpen($menu, false); }, 200);
			});
		}

		// Tabs.
		$menu.on('mouseenter', '.ux-mega-menu-tab', function () {
			if (!desktopMode($menu)) return;
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

			if (desktopMode($menu)) {
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
			if (desktopMode($menu)) activate($(this).closest('.ux-mega-menu-item'));
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

	// Screen changes: switch between button and drawer (data-drawer).
	// Media query change events (also fired when devtools emulate a size),
	// plus resize as a fallback.
	function updateAllDrawers() {
		$('.ux-mega-menu[data-drawer]').each(function () { updateDrawer($(this)); });
		// Open desktop panels follow the new width.
		$('.ux-mega-menu.is-open').each(function () { placePanel($(this)); });
	}
	SCREENS.forEach(function (screen) {
		var query = window.matchMedia(screen[1]);
		if (query.addEventListener) query.addEventListener('change', updateAllDrawers);
		else if (query.addListener) query.addListener(updateAllDrawers);
	});
	var resizeTimer = null;
	$(window).on('resize orientationchange', function () {
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(updateAllDrawers, 100);
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
