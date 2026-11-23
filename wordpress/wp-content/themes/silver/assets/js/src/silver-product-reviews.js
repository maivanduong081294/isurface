/**
 * Product reviews: the theme's layout (woocommerce/single-product/reviews-layout.php,
 * inc/woocommerce/structure-wc-product-reviews.php).
 *
 * - "Write a review": the form (#review_form) moved into a silverDialog popup,
 *   back in its place when the popup closes (what was typed stays).
 * - Stars: the label of the star hovered / chosen.
 * - Photos: checked (number, size, type) and previewed, each one removable.
 * - Sent with AJAX (admin-ajax.php?action=silver_product_review); the error
 *   in the form, next to the field; then a silverDialog message.
 * - "More reviews": the next page (action=silver_product_reviews_more).
 * - A customer photo opens in a silverDialog popup, with the other photos of
 *   the review (previous / next, arrow keys).
 */
(function (window, document) {
	'use strict';

	const ajaxUrl = () => (window.silverVars && window.silverVars.ajaxurl) || '/wp-admin/admin-ajax.php';

	function messagesOf(root) {
		try {
			return JSON.parse(root.getAttribute('data-messages') || '{}');
		} catch (e) {
			return {};
		}
	}

	function notify(message, type) {
		if (window.silverDialog) {
			return window.silverDialog.alert(message, { type: type });
		}
		window.alert(message); // eslint-disable-line no-alert
		return Promise.resolve();
	}

	/* ------------------------------------------------------------ Stars */

	function setupStars(form) {
		const stars = form.querySelector('[data-review-stars]');
		if (!stars) return;

		const inputs = Array.from(stars.querySelectorAll('.product-review-stars__input'));
		const labels = Array.from(stars.querySelectorAll('.product-review-stars__star'));
		const text = stars.querySelector('.product-review-stars__text');

		function paint(value) {
			labels.forEach((label, i) => label.classList.toggle('is-on', i < value));
			if (text) text.textContent = value ? labels[value - 1].getAttribute('data-label') : '';
		}

		const checked = () => {
			const input = inputs.find((i) => i.checked);
			return input ? parseInt(input.value, 10) : 0;
		};

		labels.forEach((label, i) => {
			label.addEventListener('mouseenter', () => paint(i + 1));
		});
		stars.addEventListener('mouseleave', () => paint(checked()));
		inputs.forEach((input) => input.addEventListener('change', () => paint(checked())));
		paint(checked());
	}

	/* ------------------------------------------------------------ Photos */

	function setupPhotos(form, root, messages) {
		const box = form.querySelector('[data-review-photos]');
		if (!box) return null;

		const input = box.querySelector('input[type="file"]');
		const list = box.querySelector('[data-review-photo-list]');
		const add = box.querySelector('.product-review-photos__add');
		const max = parseInt(root.getAttribute('data-images-max'), 10) || 3;
		const size = parseInt(root.getAttribute('data-image-size'), 10) || 1048576;
		const types = ['image/jpeg', 'image/png', 'image/webp'];
		let files = [];
		const urls = new Map();

		function sync() {
			// The input sends the photos kept (DataTransfer: the files chosen,
			// minus the ones removed).
			if (window.DataTransfer) {
				const transfer = new DataTransfer();
				files.forEach((file) => transfer.items.add(file));
				input.files = transfer.files;
			}

			list.innerHTML = '';
			files.forEach((file, index) => {
				if (!urls.has(file)) urls.set(file, URL.createObjectURL(file));
				const item = document.createElement('li');
				item.className = 'product-review-photos__item';
				const img = document.createElement('img');
				img.src = urls.get(file);
				img.alt = '';
				const remove = document.createElement('button');
				remove.type = 'button';
				remove.className = 'product-review-photos__remove';
				remove.setAttribute('aria-label', (messages.remove || 'Remove') + ' ' + (index + 1));
				remove.addEventListener('click', () => {
					URL.revokeObjectURL(urls.get(file));
					urls.delete(file);
					files = files.filter((f) => f !== file);
					sync();
					(add || input).focus();
				});
				item.appendChild(img);
				item.appendChild(remove);
				list.appendChild(item);
			});

			if (add) add.hidden = files.length >= max;
		}

		input.addEventListener('change', () => {
			const chosen = Array.from(input.files || []);
			let error = '';

			chosen.forEach((file) => {
				if (types.indexOf(file.type) === -1) {
					error = messages.type;
				} else if (file.size > size) {
					error = messages.tooBig;
				} else if (files.length >= max) {
					error = messages.tooMany;
				} else if (!files.some((f) => f.name === file.name && f.size === file.size)) {
					files.push(file);
				}
			});

			sync();
			return error ? form.dispatchEvent(new CustomEvent('review:error', { detail: { message: error } })) : null;
		});

		return {
			reset() {
				urls.forEach((url) => URL.revokeObjectURL(url));
				urls.clear();
				files = [];
				sync();
			},
		};
	}

	/* ------------------------------------------------------------ Form */

	function setupForm(root) {
		const wrapper = root.querySelector('.product-reviews__form');
		const holder = root.querySelector('[data-review-form]');
		const form = holder && holder.querySelector('form');
		if (!wrapper || !holder || !form) return;

		const messages = messagesOf(root);
		const error = form.querySelector('.product-review-form__error');
		const submit = form.querySelector('[type="submit"]');
		let popup = null;

		form.noValidate = true; // Checked here, the message in the form.
		wrapper.hidden = true; // Opened in a popup.

		setupStars(form);
		const photos = setupPhotos(form, root, messages);

		function showError(message, field) {
			error.textContent = message || messages.error || '';
			error.hidden = !error.textContent;
			const target = field ? form.querySelector('[name="' + field + '"]') : null;
			if (target) target.focus();
		}

		form.addEventListener('review:error', (event) => showError(event.detail.message));

		function open(opener) {
			if (popup) return;
			if (!window.silverDialog) {
				wrapper.hidden = false;
				holder.scrollIntoView({ behavior: 'smooth', block: 'start' });
				return;
			}
			popup = window.silverDialog.popup(holder, {
				title: messages.title,
				className: 'product-review-popup',
				closeOnOverlay: false, // Not lost on a stray click.
				onClose: () => {
					wrapper.appendChild(holder); // Back in place before the popup goes.
					popup = null;
					if (opener) opener.focus();
				},
			});
			window.setTimeout(() => {
				const first = form.querySelector('.product-review-stars__input:checked, .product-review-stars__input, input:not([type="hidden"]), textarea');
				if (first) first.focus();
			}, 60);
		}

		root.addEventListener('click', (event) => {
			const opener = event.target.closest('[data-review-open]');
			if (!opener) return;
			event.preventDefault();
			open(opener);
		});

		form.addEventListener('submit', (event) => {
			event.preventDefault();
			if (form.classList.contains('is-sending')) return;
			showError('');

			const stars = form.querySelector('[data-review-stars]');
			if (stars && form.querySelector('.product-review-stars__input[required]') && !form.querySelector('.product-review-stars__input:checked')) {
				showError(messages.rating, 'rating');
				return;
			}
			if (!form.reportValidity()) return;

			const data = new FormData(form);
			data.append('action', 'silver_product_review');

			form.classList.add('is-sending');
			submit.disabled = true;
			submit.setAttribute('aria-busy', 'true');

			fetch(ajaxUrl(), { method: 'POST', body: data, credentials: 'same-origin' })
				.then((response) => response.json())
				.then((json) => {
					const result = json && json.data ? json.data : {};
					if (!json || !json.success) {
						showError(result.message, result.field);
						return;
					}
					form.reset();
					if (photos) photos.reset();
					form.querySelectorAll('.product-review-stars__star').forEach((star) => star.classList.remove('is-on'));
					if (popup) popup.close();
					notify(result.message, 'success').then(() => {
						if (result.approved) window.location.reload();
					});
				})
				.catch(() => showError(messages.error))
				.finally(() => {
					form.classList.remove('is-sending');
					submit.disabled = false;
					submit.removeAttribute('aria-busy');
				});
		});
	}

	/* ------------------------------------------------------------ More */

	function setupMore(root) {
		root.addEventListener('click', (event) => {
			const button = event.target.closest('[data-review-more]');
			if (!button || button.disabled) return;

			const list = root.querySelector('.product-reviews__items');
			const label = button.textContent;
			const messages = messagesOf(root);
			const params = new URLSearchParams({
				action: 'silver_product_reviews_more',
				product_id: root.getAttribute('data-product-id'),
				page: button.getAttribute('data-page'),
			});

			button.disabled = true;
			button.setAttribute('aria-busy', 'true');
			button.textContent = messages.loading || label;

			fetch(ajaxUrl() + '?' + params.toString(), { credentials: 'same-origin' })
				.then((response) => response.json())
				.then((json) => {
					if (!json || !json.success) throw new Error();
					const before = list.children.length;
					list.insertAdjacentHTML('beforeend', json.data.html || '');
					// Focus to the first new review, for keyboards and readers.
					const first = list.children[before];
					if (first) {
						first.tabIndex = -1;
						first.focus({ preventScroll: true });
					}
					if (json.data.more) {
						button.setAttribute('data-page', json.data.page);
						button.textContent = label;
						button.disabled = false;
					} else {
						button.parentNode.remove();
					}
				})
				.catch(() => {
					button.textContent = label;
					button.disabled = false;
					notify(messages.error, 'error');
				})
				.finally(() => button.removeAttribute('aria-busy'));
		});
	}

	/* ------------------------------------------------------------ Photo viewer */

	function setupViewer(root) {
		root.addEventListener('click', (event) => {
			const link = event.target.closest('[data-review-photo]');
			if (!link || !window.silverDialog) return;
			event.preventDefault();

			const links = Array.from(link.closest('.product-review__photos').querySelectorAll('[data-review-photo]'));
			Silver.themePopup.photos(links, link, messagesOf(root));
		});
	}

	function init() {
		document.querySelectorAll('.product-reviews').forEach((root) => {
			if (root.dataset.reviewsReady) return;
			root.dataset.reviewsReady = '1';
			setupForm(root);
			setupMore(root);
			setupViewer(root);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})(window, document);
