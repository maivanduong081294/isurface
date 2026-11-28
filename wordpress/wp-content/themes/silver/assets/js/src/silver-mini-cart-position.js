/**
 * Keeps the mini cart dropdown (header cart, [ux_quick_cart] "Dropdown")
 * inside the screen.
 *
 * The theme's dropdown positioning (silver.js) only runs when the menu sits
 * in a ".container" at least 750px wide: a quick cart placed in a UX Builder
 * row, or on a tablet, kept its default position (left: -15px) and opened
 * past the right edge of the screen. Once the dropdown is open, it is moved
 * back with `translate` (independent of the theme's margins/transforms).
 */
(function () {
	'use strict';

	const GAP = 8;
	const SELECTOR = 'li.has-dropdown';

	function cartDropdown(li) {
		const dropdown = li && li.querySelector(':scope > .nav-dropdown');
		return dropdown && dropdown.querySelector(':scope > .widget_shopping_cart') ? dropdown : null;
	}

	function clamp(dropdown) {
		dropdown.style.translate = '';
		const rect = dropdown.getBoundingClientRect();
		const width = document.documentElement.clientWidth;

		// Closed (moved off screen by the theme) or not laid out.
		if (!rect.width || rect.right < 0 || rect.left > width) {
			return;
		}

		let dx = 0;
		if (rect.right > width - GAP) {
			dx = width - GAP - rect.right;
		}
		if (rect.left + dx < GAP) {
			dx = GAP - rect.left;
		}
		if (dx) {
			dropdown.style.translate = Math.round(dx) + 'px 0';
		}
	}

	// After the theme's own handlers have set their margins. The theme opens
	// the dropdown after a hover delay: measure again a little later.
	function schedule(li) {
		const dropdown = cartDropdown(li);
		if (!dropdown) {
			return;
		}
		window.requestAnimationFrame(() => window.requestAnimationFrame(() => clamp(dropdown)));
		window.setTimeout(() => clamp(dropdown), 250);
	}

	['mouseover', 'focusin', 'click', 'touchstart'].forEach((type) => {
		document.addEventListener(type, (event) => {
			const li = event.target instanceof Element ? event.target.closest(SELECTOR) : null;
			if (li) {
				schedule(li);
			}
		}, { passive: true });
	});

	// Opened by the theme (class "current-dropdown" on the li).
	const observer = new MutationObserver((mutations) => {
		mutations.forEach((mutation) => {
			if (mutation.target.classList.contains('current-dropdown')) {
				schedule(mutation.target);
			}
		});
	});

	function observe() {
		document.querySelectorAll(SELECTOR).forEach((li) => {
			if (cartDropdown(li)) {
				observer.observe(li, { attributes: true, attributeFilter: ['class'] });
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', observe);
	} else {
		observe();
	}

	let resizeTimer = 0;
	window.addEventListener('resize', () => {
		window.clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(() => {
			document.querySelectorAll(SELECTOR).forEach(schedule);
		}, 150);
	});
})();
