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
 * - UX Builder (.ux-mega-menu-builder): closed like on the site; a click on
 *   the button / icon opens it, a click on a tab shows its pane (no hover).
 *   The open state and the open item are kept when the builder renders the
 *   element again; selecting an element inside the menu (canvas or layers)
 *   opens it on that item, selecting an element outside closes it.
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

	function isBuilder($menu) {
		return $menu.hasClass('ux-mega-menu-builder');
	}

	// UX Builder: state per element ID (the builder keeps the ID, not the DOM).
	var builderStates = {};

	function builderState($menu) {
		var id = $menu.attr('id') || '';
		if (!builderStates[id]) {
			// tabItem / tabTime: the item whose tab was just clicked.
			builderStates[id] = { open: false, active: null, selected: null, tabItem: null, tabTime: 0 };
		}
		return builderStates[id];
	}

	// Items of this menu only (not of a menu nested in a pane).
	function itemsOf($menu) {
		var menu = $menu[0];
		return $menu.find('.ux-mega-menu-item').filter(function () {
			return $(this).closest('.ux-mega-menu')[0] === menu;
		});
	}

	// Display "Icon + drawer" on the screens listed in data-drawer.
	function updateDrawer($menu) {
		var screens = String($menu.data('drawer') || '').split(' ');
		var drawer = screens.indexOf(currentScreen()) !== -1;
		if (drawer === $menu.hasClass('is-drawer')) return;
		if ($menu.hasClass('is-open') && !isBuilder($menu)) setOpen($menu, false);
		$menu.toggleClass('is-drawer', drawer);
	}

	// Desktop behaviour (hover, tabs | panes) only when it is not a drawer.
	function desktopMode($menu) {
		return isDesktop() && !$menu.hasClass('is-drawer');
	}

	/**
	 * Whether an element holds or cuts its fixed descendants (instead of the
	 * screen): transform (and translate / rotate / scale), filter, perspective,
	 * will-change of those, contain, container, clip-path, mask...
	 */
	function holdsFixed(style) {
		var has = function (value) { return value && value !== 'none'; };
		return has(style.transform) || has(style.translate) || has(style.rotate) || has(style.scale)
			|| has(style.filter) || has(style.backdropFilter) || has(style.webkitBackdropFilter)
			|| has(style.perspective) || has(style.clipPath) || has(style.maskImage) || has(style.webkitMaskImage)
			|| has(style.offsetPath) || style.transformStyle === 'preserve-3d'
			|| /transform|translate|rotate|scale|filter|perspective|contents/.test(style.willChange || '')
			|| /paint|layout|strict|content/.test(style.contain || '')
			|| (style.contentVisibility && style.contentVisibility !== 'visible')
			|| (style.containerType && style.containerType !== 'normal');
	}

	/**
	 * UX Builder: the elements around the menu (the builder's <content> tags
	 * with `transform: translateZ(0)`, a column with an animation...) would
	 * hold the fixed drawer (cut to their box) and put the dropdown under the
	 * next sections. While a menu is open they lose those properties
	 * (.ux-mega-menu-unclip); on the site there are none of those tags.
	 */
	function unclipBuilder($menu, open) {
		if (open) {
			var found = [];
			for (var el = $menu[0].parentElement; el; el = el.parentElement) {
				if (!el.classList.contains('ux-mega-menu-unclip') && holdsFixed(window.getComputedStyle(el))) {
					el.classList.add('ux-mega-menu-unclip');
					found.push(el);
				}
			}
			if (found.length && window.console) {
				window.console.info('[ux-mega-menu] builder: elements holding the panel, unclipped while open:', found);
			}
			return;
		}
		if (!$('.ux-mega-menu-builder.is-open').length) {
			$('.ux-mega-menu-unclip').removeClass('ux-mega-menu-unclip');
		}
	}

	function setOpen($menu, open) {
		$menu.toggleClass('is-open', open);
		$menu.children('.ux-mega-menu-trigger').attr('aria-expanded', open ? 'true' : 'false');

		if (isBuilder($menu)) {
			builderState($menu).open = open;
			unclipBuilder($menu, open);
		} else {
			// Drawer: no page scroll behind it (not in the builder).
			$('html').toggleClass('ux-mega-menu-locked', $('.ux-mega-menu.is-drawer.is-open').not('.ux-mega-menu-builder').length > 0);
		}

		if (open) placePanel($menu);
	}

	var EDGE = 16; // Minimum space between the panel and the screen edges.

	/**
	 * Desktop panel position (not the drawer). The panel stays inside the
	 * container the menu is in (header / row) and inside the screen:
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

		if (!desktopMode($menu)) return;

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
		var $menu = $item.closest('.ux-mega-menu');
		var $items = itemsOf($menu);
		$items.not($item).removeClass('is-active').children('.ux-mega-menu-tab').attr('aria-expanded', 'false');
		$item.addClass('is-active').children('.ux-mega-menu-tab').attr('aria-expanded', 'true');
		if (isBuilder($menu)) builderState($menu).active = $item.attr('id') || null;
	}

	function collapse($item) {
		var $menu = $item.closest('.ux-mega-menu');
		$item.removeClass('is-active').children('.ux-mega-menu-tab').attr('aria-expanded', 'false');
		if (isBuilder($menu)) builderState($menu).active = null;
	}

	// A click on a tab (builder: no link, the item is edited instead).
	function clickTab($menu, $tab) {
		var $item = $tab.closest('.ux-mega-menu-item');

		if (desktopMode($menu)) {
			// The pane is already shown on hover: a click follows the link.
			if ($item.hasClass('is-active') && $tab.data('href') && !isBuilder($menu)) {
				window.location.href = $tab.data('href');
				return;
			}
			activate($item);
			return;
		}

		// Mobile: accordion.
		if ($item.hasClass('is-active')) {
			collapse($item);
		} else {
			activate($item);
		}
	}

	/**
	 * UX Builder: puts back the state after a render (the builder replaces the
	 * element's DOM) and follows the selected element. Runs again on every DOM
	 * change, so it only changes what differs (no endless loop).
	 */
	function syncBuilder($menu) {
		var menu = $menu[0];
		var state = builderState($menu);
		var selected = document.querySelector('.uxb-selected');

		// Selection changed (none while the builder renders again: ignored).
		if (selected && selected !== state.selected) {
			state.selected = selected;
			if (selected !== menu && menu.contains(selected)) {
				// An element inside: open the menu on its item (not when a tab
				// was just clicked: the click itself opens or closes the pane).
				state.open = true;
				var $selectedItem = $(selected).closest('.ux-mega-menu-item');
				var fromTab = $selectedItem.length && $selectedItem.attr('id') === state.tabItem && Date.now() - state.tabTime < 500;
				if ($selectedItem.length && !fromTab) {
					state.active = $selectedItem.attr('id') || null;
				}
			} else if (selected !== menu) {
				// An element outside (or around) the menu.
				state.open = false;
			}
		}

		if ($menu.hasClass('is-open') !== state.open) setOpen($menu, state.open);
		// The builder may render the elements around it again.
		else if (state.open) unclipBuilder($menu, true);

		var $items = itemsOf($menu);
		if (!$items.length) return;

		// Tabs | panes grid: one row per tab (the server counted none, the
		// builder renders the items on their own).
		var inner = $menu.children('.ux-mega-menu-panel').children('.ux-mega-menu-panel-inner')[0];
		var rows = 'repeat(' + $items.length + ', min-content) 1fr';
		if (inner && inner.style.gridTemplateRows !== rows) inner.style.gridTemplateRows = rows;

		var $active = $items.filter('.is-active');
		var $remembered = state.active ? $items.filter(function () { return this.id === state.active; }) : $();

		if ($remembered.length) {
			if (!$remembered.hasClass('is-active') || $active.length !== 1) activate($remembered);
		} else if (desktopMode($menu)) {
			// Tabs | panes: always one pane shown.
			if ($active.length !== 1) activate($active.length ? $active.first() : $items.first());
		} else if ($active.length > 1) {
			activate($active.first());
		}
	}

	// UX Builder: follow its renders and its selection (class uxb-selected).
	var builderObserver = null;
	var builderTimer = null;

	function syncAllBuilders() {
		$('.ux-mega-menu-builder').each(function () {
			init(this);
			syncBuilder($(this));
		});
	}

	function watchBuilder() {
		if (builderObserver || !window.MutationObserver) return;
		builderObserver = new MutationObserver(function () {
			clearTimeout(builderTimer);
			builderTimer = setTimeout(syncAllBuilders, 40);
		});
		builderObserver.observe(document.body, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
	}

	function init(menu) {
		var $menu = $(menu);
		var builder = isBuilder($menu);
		var hoverMode = $menu.data('trigger') === 'hover' && !builder;
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
			if (builder || !desktopMode($menu)) return;
			var $item = $(this).closest('.ux-mega-menu-item');
			clearTimeout(tabTimer);
			tabTimer = setTimeout(function () { activate($item); }, HOVER_DELAY);
		}).on('mouseleave', '.ux-mega-menu-tab', function () {
			clearTimeout(tabTimer);
		});

		if (builder) {
			// The builder stops the click on each element (to select it), so
			// the tab click never reaches the menu: listen while it goes down.
			menu.addEventListener('click', function (event) {
				var tab = event.target && event.target.closest ? event.target.closest('.ux-mega-menu-tab') : null;
				if (!tab || $(tab).closest('.ux-mega-menu')[0] !== menu) return;
				var state = builderState($menu);
				state.tabItem = $(tab).closest('.ux-mega-menu-item').attr('id') || null;
				state.tabTime = Date.now();
				clickTab($menu, $(tab));
			}, true);
		} else {
			$menu.on('click', '.ux-mega-menu-tab', function (event) {
				event.preventDefault();
				clickTab($menu, $(this));
			});

			$menu.on('focus', '.ux-mega-menu-tab', function () {
				if (desktopMode($menu)) activate($(this).closest('.ux-mega-menu-item'));
			});
		}

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

		if (builder) {
			watchBuilder();
			syncBuilder($menu);
		}
	}

	// Click outside closes every open menu (not in the builder: there a click
	// selects an element, see syncBuilder()).
	$(document).on('click', function (event) {
		$('.ux-mega-menu.is-open').not('.ux-mega-menu-builder').each(function () {
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
		// Builder: tabs | panes need an open item, the accordion does not.
		$('.ux-mega-menu-builder').each(function () { syncBuilder($(this)); });
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
			var $context = $(context || document);
			// The context can be the menu itself (UX Builder re-render).
			$context.find('.ux-mega-menu').addBack('.ux-mega-menu').each(function () {
				init(this);
			});
			// Or one of its items (the builder renders an item on its own).
			$context.closest('.ux-mega-menu-builder').each(function () {
				init(this);
				syncBuilder($(this));
			});
		}
	});
})(jQuery);
