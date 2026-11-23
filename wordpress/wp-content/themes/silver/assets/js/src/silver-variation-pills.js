/**
 * Product page buy box (inc/woocommerce/structure-wc-product-buy-box.php).
 *
 * - Attribute buttons (.sv-variation-pills): a click selects the value in the
 *   hidden dropdown of WooCommerce (and a second click clears it); the
 *   buttons follow the dropdowns: selected, unavailable combinations.
 * - Price of the card: the price and the sale badge of the chosen variation,
 *   back to the price range when the choice is cleared.
 */
Silver.behavior('variation-pills', {
	attach(context) {
		jQuery('form.variations_form', context).each((index, form) => {
			if (form.dataset.svPills) {
				return;
			}
			form.dataset.svPills = '1';

			const $form = jQuery(form);
			const selectOf = (group) => {
				const name = group.getAttribute('data-attribute_name');
				return Array.from(form.querySelectorAll('select')).find((select) => select.name === name) || null;
			};

			const sync = () => {
				form.querySelectorAll('.sv-variation-pills').forEach((group) => {
					const select = selectOf(group);
					if (!select) {
						return;
					}
					group.querySelectorAll('.sv-variation-pill').forEach((pill) => {
						const value = pill.getAttribute('data-value');
						const option = Array.from(select.options).find((item) => item.value === value);
						const available = !!option && !option.disabled;
						const checked = select.value === value && '' !== value;

						pill.classList.toggle('is-selected', checked);
						pill.classList.toggle('is-disabled', !available);
						pill.setAttribute('aria-checked', checked ? 'true' : 'false');
						pill.setAttribute('aria-disabled', available ? 'false' : 'true');
					});
				});
			};

			form.addEventListener('click', (event) => {
				const pill = event.target.closest('.sv-variation-pill');
				if (!pill || !form.contains(pill) || pill.classList.contains('is-disabled')) {
					return;
				}
				event.preventDefault();

				const select = selectOf(pill.closest('.sv-variation-pills'));
				if (!select) {
					return;
				}
				const value = pill.getAttribute('data-value');
				jQuery(select).val(select.value === value ? '' : value).trigger('change');
				sync();
			});

			// Arrow keys move between the buttons of a group.
			form.addEventListener('keydown', (event) => {
				const pill = event.target.closest('.sv-variation-pill');
				if (!pill || !['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'].includes(event.key)) {
					return;
				}
				const pills = Array.from(pill.parentNode.querySelectorAll('.sv-variation-pill'));
				const step = 'ArrowLeft' === event.key || 'ArrowUp' === event.key ? -1 : 1;
				const next = pills[(pills.indexOf(pill) + step + pills.length) % pills.length];
				event.preventDefault();
				next.focus();
			});

			$form.on('woocommerce_update_variation_values reset_data found_variation check_variations change', sync);

			// Price of the card.
			const box = form.closest('.product-buy-box');
			const wrapper = box ? box.querySelector('.price-wrapper') : null;
			if (wrapper) {
				const original = wrapper.innerHTML;
				const format = wrapper.getAttribute('data-badge-format') || '-{value}%';

				$form.on('found_variation', (event, variation) => {
					if (!variation || !variation.price_html) {
						wrapper.innerHTML = original;
						return;
					}
					const regular = parseFloat(variation.display_regular_price);
					const price = parseFloat(variation.display_price);
					const percent = regular > price && regular > 0 ? Math.round(((regular - price) / regular) * 100) : 0;
					const classes = ['price', 'product-page-price'];
					if (percent > 0) {
						classes.push('price-on-sale');
					}

					// price_html comes from WooCommerce (escaped there).
					wrapper.innerHTML = '<div class="' + classes.join(' ') + '">' + variation.price_html + '</div>';
					if (percent > 0) {
						const badge = document.createElement('span');
						badge.className = 'product-price-badge';
						badge.textContent = format.replace('{value}', String(percent));
						wrapper.appendChild(badge);
					}
				});

				$form.on('reset_data', () => {
					wrapper.innerHTML = original;
				});
			}

			// SKU row (single-product/meta.php): hidden when the product has no
			// SKU, shown for a chosen variation that has one (WooCommerce
			// writes the SKU itself).
			const product = form.closest('.product');
			const skuRow = product ? product.querySelector('.product_meta .sku_wrapper') : null;
			if (skuRow) {
				const skuHidden = skuRow.hidden;
				$form.on('found_variation', (event, variation) => {
					skuRow.hidden = !(variation && variation.sku) && skuHidden;
				});
				$form.on('reset_data', () => {
					skuRow.hidden = skuHidden;
				});
			}

			// Sticky add to cart bar (silver_sticky_add_to_cart_template()), for
			// the form of the product of the page: once a variation is chosen,
			// its price, the quantity, "Add to cart" and "Buy now" (they press
			// the buttons of this form); back to "Từ 15 ₫" and "Select options"
			// when the choice is cleared.
			const sticky = document.querySelector('.sticky-add-to-cart');
			const productId = sticky ? sticky.getAttribute('data-product-id') : '';
			if (sticky && productId && form.closest('#product-' + productId)) {
				const stickyPrice = sticky.querySelector('.sticky-add-to-cart__price');
				const stickyActions = sticky.querySelector('.sticky-add-to-cart__variation');
				const selectButton = sticky.querySelector('.sticky-add-to-cart-select-options-button');
				const stickyOriginal = stickyPrice ? stickyPrice.innerHTML : '';

				const showActions = (show) => {
					if (stickyActions) {
						stickyActions.hidden = !show;
					}
					if (selectButton) {
						selectButton.hidden = show;
					}
				};

				$form.on('found_variation', (event, variation) => {
					if (stickyPrice) {
						stickyPrice.innerHTML = variation && variation.price_html ? variation.price_html : stickyOriginal;
					}
					showActions(!!variation && variation.is_purchasable !== false && variation.is_in_stock !== false);
				});

				$form.on('reset_data', () => {
					if (stickyPrice) {
						stickyPrice.innerHTML = stickyOriginal;
					}
					showActions(false);
				});

				sticky.addEventListener('click', (event) => {
					const add = event.target.closest('.sticky-add-to-cart__add');
					const buyNow = event.target.closest('.sticky-add-to-cart__buy-now');
					if (!add && !buyNow) {
						return;
					}
					event.preventDefault();
					const target = form.querySelector(add ? '.single_add_to_cart_button' : '.ux-buy-now-button');
					if (target && !target.classList.contains('disabled') && !target.disabled) {
						target.click();
					}
				});
			}

			sync();
		});
	},
});
