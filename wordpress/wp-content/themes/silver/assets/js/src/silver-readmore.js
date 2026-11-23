/**
 * Read more element ([ux_readmore]...[/ux_readmore], inc/shortcodes/ux_readmore.php).
 *
 * - Cuts the content to data-height when it is taller than that plus 80px
 *   (.is-collapsible); measured again on resize, when images load and when
 *   its size changes (a hidden product tab shown).
 * - The button expands / collapses it (height animated, unless "reduce
 *   motion"); after a collapse the page scrolls back to the element when its
 *   top went out of view.
 * - Focus moving into the hidden part (Tab) expands it.
 * - Other scripts expand it at once with the "ux-readmore:expand" event (the
 *   table of contents does, before scrolling to a heading inside it).
 *
 * Registered as a Silver behavior so it also runs on content re-rendered by
 * UX Builder.
 */
(function ($) {
	'use strict';

	if (!window.Silver || typeof window.Silver.behavior !== 'function') {
		return;
	}

	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var EXTRA = 80; // No cut for less than this.
	var DURATION = 350;

	function setup(el) {
		var content = el.querySelector('.ux-readmore__content');
		var button = el.querySelector('.ux-readmore__toggle');
		var label = button && button.querySelector('.ux-readmore__label');
		var limit = parseInt(el.getAttribute('data-height'), 10) || 400;
		var timer = null;

		if (!content || !button) return;

		function isCollapsed() {
			return el.classList.contains('is-collapsible') && !el.classList.contains('is-expanded');
		}

		function measure() {
			var collapsible = content.scrollHeight > limit + EXTRA;

			el.classList.toggle('is-collapsible', collapsible);
			if (!collapsible) {
				el.classList.remove('is-expanded');
				button.setAttribute('aria-expanded', 'false');
			}
		}

		function setLabel(open) {
			button.setAttribute('aria-expanded', open ? 'true' : 'false');
			if (label) {
				label.textContent = button.getAttribute(open ? 'data-less' : 'data-more');
			}
		}

		function finish() {
			content.style.maxHeight = '';
			content.style.transition = '';
			content.style.overflow = '';
			timer = null;
			$(el).trigger('ux-readmore:toggle'); // The page height changed.
		}

		function setExpanded(open, animate) {
			if (!el.classList.contains('is-collapsible') || open === el.classList.contains('is-expanded')) {
				return;
			}
			if (timer) {
				window.clearTimeout(timer);
				finish();
			}

			animate = animate && !reduceMotion;
			setLabel(open);

			if (!animate) {
				el.classList.toggle('is-expanded', open);
				$(el).trigger('ux-readmore:toggle');
				return;
			}

			// From the current height to the target one, then back to the CSS.
			var from = content.getBoundingClientRect().height;
			el.classList.toggle('is-expanded', open);
			var to = open ? content.scrollHeight : limit;

			content.style.overflow = 'hidden'; // Clipped while it animates.
			content.style.maxHeight = from + 'px';
			content.getBoundingClientRect(); // Reflow.
			content.style.transition = 'max-height ' + DURATION + 'ms ease';
			content.style.maxHeight = to + 'px';
			timer = window.setTimeout(finish, DURATION + 20);
		}

		// Collapsed while reading further down: back to the element, once the
		// height has settled (the page moves while it animates).
		function scrollBack() {
			var top = el.getBoundingClientRect().top;
			if (top < 0) {
				window.scrollTo({ top: top + window.pageYOffset - 120, behavior: reduceMotion ? 'auto' : 'smooth' });
			}
		}

		button.addEventListener('click', function () {
			var open = !el.classList.contains('is-expanded');

			setExpanded(open, true);

			if (!open) {
				window.setTimeout(scrollBack, reduceMotion ? 0 : DURATION + 40);
			}
		});

		// Keyboard only: a click on a link inside (the table of contents) also
		// focuses it, and opens the element only when its target is hidden.
		content.addEventListener('focusin', function (e) {
			var keyboard = true;
			try {
				keyboard = e.target.matches(':focus-visible');
			} catch (err) {
				keyboard = true;
			}
			if (keyboard && isCollapsed()) setExpanded(true, false);
		});

		$(el).on('ux-readmore:expand', function () {
			setExpanded(true, false);
		});

		// Images change the height once loaded.
		Array.prototype.forEach.call(content.querySelectorAll('img'), function (img) {
			if (!img.complete) img.addEventListener('load', measure, { once: true });
		});

		var resizeTimer = null;
		window.addEventListener('resize', function () {
			window.clearTimeout(resizeTimer);
			resizeTimer = window.setTimeout(measure, 150);
		});

		// Measured again when the element changes size: shown at last (a product
		// tab that was hidden measured 0), content that opens inside it. Not
		// while it animates.
		if ('ResizeObserver' in window) {
			new ResizeObserver(function () {
				if (!timer) measure();
			}).observe(el);
		}

		measure();
	}

	window.Silver.behavior('ux-readmore', {
		attach: function (context) {
			$(context || document).find('.ux-readmore').addBack('.ux-readmore').each(function () {
				if ($(this).data('uxReadmore') || this.classList.contains('ux-readmore-builder')) return;
				$(this).data('uxReadmore', true);
				setup(this);
			});
		}
	});
})(jQuery);
