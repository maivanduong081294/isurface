/**
 * "Added to cart" toast (inc/woocommerce/structure-wc-cart-notices.php):
 * "Đã thêm Engu Mascot Tee — Glacier White / M vào giỏ hàng" at the bottom
 * of the screen for a few seconds.
 *
 * - AJAX adds (added_to_cart: single product with Advanced > AJAX add to
 *   cart, archive buttons): the name of the product, and for a variation the
 *   chosen options, from the form of the button.
 * - A product page loaded after adding without AJAX: the label printed by
 *   PHP (data-message).
 */
(function () {
	'use strict';

	const DURATION = 3500;
	let timer = 0;
	let clearTimer = 0;

	const toast = () => document.querySelector('.sv-toast');

	function show(label) {
		const el = toast();
		if (!el || !label) {
			return;
		}

		const text = el.querySelector('.sv-toast__text');
		const template = el.getAttribute('data-template') || '%s';

		window.clearTimeout(timer);
		window.clearTimeout(clearTimer);

		// Text first, then visible: the live region announces it.
		text.textContent = template.replace('%s', label);
		el.classList.add('is-visible');

		timer = window.setTimeout(() => {
			el.classList.remove('is-visible');
			// Empty once hidden, so screen readers do not find it later.
			clearTimer = window.setTimeout(() => {
				text.textContent = '';
			}, 400);
		}, DURATION);
	}

	const clean = (value) => (value || '').replace(/\s+/g, ' ').trim();

	function labelOf(button) {
		if (!button) {
			return '';
		}

		// Single product (the sticky bar presses the buttons of this form).
		const form = button.closest('form.cart');
		if (form) {
			const product = form.closest('.product');
			const titleEl = product ? product.querySelector('.product_title, .product-title') : null;
			const title = clean(titleEl ? titleEl.textContent : '');
			const values = Array.from(form.querySelectorAll('table.variations select'))
				.filter((select) => select.value)
				.map((select) => clean(select.options[select.selectedIndex].text));

			return values.length ? title + ' — ' + values.join(' / ') : title;
		}

		// Archive buttons.
		const card = button.closest('.product-small, .product');
		const nameEl = card ? card.querySelector('.woocommerce-loop-product__title, .product-title, .name') : null;

		return clean(button.getAttribute('data-product_name')) || clean(nameEl ? nameEl.textContent : '');
	}

	if (window.jQuery) {
		window.jQuery(document.body).on('added_to_cart', (event, fragments, hash, $button) => {
			show(labelOf($button && $button[0] ? $button[0] : null));
		});
	}

	const fromPage = () => {
		const el = toast();
		if (el && el.getAttribute('data-message')) {
			window.setTimeout(() => show(el.getAttribute('data-message')), 300);
		}
	};

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', fromPage);
	} else {
		fromPage();
	}
})();
