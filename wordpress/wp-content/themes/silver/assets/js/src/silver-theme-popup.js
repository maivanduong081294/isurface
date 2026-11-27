/* Storefront media, shortcode content, drawers and WooCommerce quick view. */
(() => {
	const $ = window.jQuery;
	let current;

	function open(options) {
		const item = options.items;
		// Quick view supplies server-rendered HTML. Shortcodes and drawers supply
		// an existing node which must retain its form state and event listeners.
		const content = typeof item.src !== 'string' ? item.src :
			item.src.startsWith('#') ? document.getElementById(item.src.slice(1)) : $(item.src)[0];
		if (!content) return;
		current?.dialog.close();
		const marker = content.isConnected ? document.createComment('silver-dialog') : null;
		if (marker) content.before(marker);
		const hidden = content.classList.contains('mfp-hide');
		content.classList.remove('mfp-hide');

		const context = { st: options, content: $(content) };
		const callback = (name) => options.callbacks?.[name]?.call(context);
		callback('beforeOpen');
		const dialog = silverDialog.popup(content, {
			className: 'silver-theme-popup ' + (options.mainClass || ''),
			onClose() {
				callback('beforeClose');
				if (hidden) content.classList.add('mfp-hide');
				if (marker) marker.replaceWith(content);
				current = null;
				callback('afterClose');
			},
		});
		const box = dialog.element.querySelector('.silver-dialog__box');
		// Existing drawer layouts and extension callbacks style these elements.
		box.classList.add('mfp-content');
		dialog.element.classList.add('mfp-ready');
		if (!dialog.element.classList.contains('off-canvas')) {
			const width = getComputedStyle(content).maxWidth;
			if (width !== 'none' && !width.includes('%')) box.style.setProperty('--silver-content-width', width);
		}
		current = { item, dialog };
		callback('open');
		if (options.focus && options.focus !== 'no-focus') {
			setTimeout(() => current?.dialog === dialog && content.querySelector(options.focus)?.focus(), 40);
		}
	}

	function photos(links, link, messages) {
		let index = links.indexOf(link);
		const view = document.createElement('div');
		view.className = 'product-review-viewer';
		const img = document.createElement('img');
		img.className = 'product-review-viewer__img';
		const caption = document.createElement('p');
		const counter = document.createElement('p');
		counter.className = 'product-review-viewer__count';
		counter.setAttribute('aria-live', 'polite');
		const stage = document.createElement('div');
		stage.className = 'product-review-viewer__stage';
		stage.append(img);
		caption.className = 'product-review-viewer__caption';
		view.append(stage, caption);
		const thumbs = document.createElement('div');
		thumbs.className = 'product-review-viewer__thumbs';

		function show(next) {
			index = (next + links.length) % links.length;
			const thumb = links[index].querySelector('img');
			img.src = thumb?.dataset.large_image || links[index].href;
			img.alt = thumb?.alt || '';
			caption.textContent = thumb?.dataset.caption || links[index].title || '';
			caption.hidden = !caption.textContent;
			counter.textContent = (index + 1) + ' / ' + links.length;
			Array.from(thumbs.children).forEach((button, i) => {
				button.setAttribute('aria-current', String(i === index));
			});
			const active = thumbs.children[index];
			if (active) thumbs.scrollLeft += active.getBoundingClientRect().left - thumbs.getBoundingClientRect().left - (thumbs.clientWidth - active.offsetWidth) / 2;
		}
		if (links.length > 1) {
			const footer = document.createElement('div');
			footer.className = 'product-review-viewer__footer';
			links.forEach((entry, i) => {
				const button = document.createElement('button');
				button.type = 'button';
				button.setAttribute('aria-label', messages.photo + ' ' + (i + 1) + ' / ' + links.length);
				const preview = new Image();
				const source = entry.querySelector('img');
				const galleryThumb = entry.closest('.woocommerce-product-gallery__image')?.dataset.thumb;
				preview.addEventListener('error', () => {
					if (preview.src !== entry.href) preview.src = entry.href;
				}, { once: true });
				preview.src = galleryThumb || source?.dataset.src || source?.currentSrc || source?.src || entry.href;
				preview.alt = '';
				button.append(preview);
				button.onclick = () => show(i);
				thumbs.append(button);
			});
			footer.append(thumbs);
			for (const [name, step] of [['prev', -1], ['next', 1]]) {
				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'product-review-viewer__nav product-review-viewer__nav--' + name;
				button.setAttribute('aria-label', messages[name]);
				button.onclick = () => show(index + step);
				footer.append(button);
				if (step < 0) footer.append(counter);
			}
			view.append(footer);
		}
		const dialog = silverDialog.popup(view, {
			title: link.closest('.woocommerce-product-gallery') ? document.querySelector('h1.product-title, h1.product_title')?.textContent.trim() || messages.photo : messages.photo,
			html: false,
			className: 'product-review-viewer-popup',
		});
		show(index); // Centre the selected thumbnail after the strip is mounted.
		dialog.element.addEventListener('keydown', (event) => {
			if (event.key === 'ArrowLeft') show(index - 1);
			else if (event.key === 'ArrowRight') show(index + 1);
			else return;
			event.preventDefault();
		});
	}

	function videoPlayer(link) {
		const url = new URL(link.href);
		if (!['http:', 'https:'].includes(url.protocol)) return false;
		let media;
		if (/\.(mp4|webm)$/i.test(url.pathname)) {
			media = document.createElement('video');
			media.controls = media.autoplay = media.playsInline = true;
		} else {
			const host = url.hostname.replace(/^(www\.|m\.)/, '');
			if (['youtube.com', 'youtu.be', 'youtube-nocookie.com'].includes(host)) {
				const id = url.searchParams.get('v') || url.pathname.split('/').filter(Boolean).pop();
				if (!/^[\w-]{11}$/.test(id || '')) return false;
				url.href = 'https://www.youtube.com/embed/' + id + '?autoplay=1';
			} else if (['vimeo.com', 'player.vimeo.com'].includes(host)) {
				const id = url.pathname.split('/').filter(Boolean).pop();
				if (!/^\d+$/.test(id || '')) return false;
				url.href = 'https://player.vimeo.com/video/' + id + '?autoplay=1';
			}
			media = document.createElement('iframe');
			media.allow = 'autoplay; fullscreen';
			media.title = link.title || link.textContent.trim() || 'Video';
		}
		media.src = url.href;
		return media;
	}

	function video(link) {
		const media = videoPlayer(link);
		if (!media) return false;
		$('.slider .is-selected .video').trigger('pause');
		silverDialog.popup(media, {
			className: 'silver-theme-video',
			onClose() {
				media.remove();
				if (media.pause) media.pause();
				$('.slider .is-selected .video').trigger('play');
			},
		});
		return true;
	}

	function galleryFor(control) {
		let scope = control.parentElement;
		while (scope && !scope.matches('body')) {
			if (scope.matches('.woocommerce-product-gallery')) return scope;
			const gallery = scope.querySelector('.woocommerce-product-gallery');
			if (gallery) return gallery;
			scope = scope.parentElement;
		}
	}

	function playInGallery(link) {
		const gallery = galleryFor(link);
		if (!gallery) return false;
		const existing = gallery.querySelector('.gallery-video-player');
		if (existing) {
			existing.querySelector('button').click();
			return true;
		}
		const media = videoPlayer(link);
		if (!media) return false;
		const panel = document.createElement('div');
		panel.className = 'gallery-video-player';
		const preview = gallery.querySelector('.flex-viewport, .woocommerce-product-gallery__wrapper');
		if (preview) {
			const {width, height} = preview.getBoundingClientRect();
			if (width && height) panel.style.aspectRatio = width + ' / ' + height;
			panel.style.marginBlockEnd = getComputedStyle(preview).marginBlockEnd;
		}
		const close = document.createElement('button');
		close.type = 'button';
		close.textContent = '×';
		close.setAttribute('aria-label', link.dataset.closeLabel);
		const dismiss = (restoreFocus) => {
			if (media.pause) media.pause();
			panel.remove();
			gallery.classList.remove('gallery-video-playing');
			link.setAttribute('aria-expanded', 'false');
			if (restoreFocus) link.focus();
		};
		panel.addEventListener('gallery-video-dismiss', () => dismiss(false));
		close.onclick = () => dismiss(true);
		panel.onkeydown = (event) => {
			if (event.key === 'Escape') close.click();
		};
		panel.append(media, close);
		gallery.prepend(panel);
		gallery.classList.add('gallery-video-playing');
		link.setAttribute('aria-expanded', 'true');
		close.focus({preventScroll:true});
		return true;
	}

	document.addEventListener('click', (event) => {
		const thumb = event.target.closest('.product-thumbnails, .flex-control-thumbs');
		if (thumb) galleryFor(thumb)?.querySelector('.gallery-video-player')?.dispatchEvent(new Event('gallery-video-dismiss'));
	}, true);

	const productImage = '.woocommerce-product-gallery__image:not(.clone) > a[href]';
	const imageFile = /\.(jpe?g|png|gif|webp|avif)$/i;
	document.addEventListener('click', (event) => {
		if (event.defaultPrevented || event.button || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
		const link = event.target.closest('a[href]');
		if (!link || link.matches('[data-review-photo], [data-open], [target="_blank"], [download]')) return;
		let selected = link;
		let links;
		const gallery = link.closest('.woocommerce-product-gallery');
		if (link.hasAttribute('data-gallery-video')) {
			if (playInGallery(link)) event.preventDefault();
			return;
		} else if (link.matches('.zoom-button') || (gallery && link.matches(productImage))) {
			if (!document.body.classList.contains('has-lightbox')) {
				event.preventDefault();
				return;
			}
			const scope = gallery || link.closest('.product-images, .product-gallery-default');
			if (!scope) return;
			links = Array.from(scope.querySelectorAll(productImage));
			if (link.matches('.zoom-button')) selected = scope.querySelector('.is-selected a, .flex-active-slide a') || links[0];
			if (!links.includes(selected)) return;
		} else if (link.matches('.open-video, .button') && (link.matches('.open-video') || /youtu\.be\/|youtube\.com\/|vimeo\.com\/|\.(mp4|webm)(\?|$)/i.test(link.href))) {
			if (document.documentElement.getAttribute('ng-app') !== 'uxBuilder' && video(link)) event.preventDefault();
			return;
		} else {
			if (!link.closest('.lightbox')) return;
			const group = link.closest('.lightbox-multi-gallery, .gallery, .row-slider, .row');
			const isGallery = link.matches('.lightbox-gallery') || !!link.closest('.lightbox-multi-gallery, .gallery');
			const matchesImage = (a) => imageFile.test(a.pathname) || a.matches('.lightbox-gallery, .image-lightbox');
			if (!matchesImage(link) || (!isGallery && !link.matches('.image-lightbox') && !link.closest('.entry-content, .wp-block-image, [id^="attachment"]'))) return;
			links = isGallery && group ? Array.from(group.querySelectorAll('a[href]')).filter(a =>
				matchesImage(a) && !a.matches('[data-review-photo], [target="_blank"]') &&
				(a.matches('.lightbox-gallery') || a.closest('.lightbox-multi-gallery, .gallery'))
			) : [link];
		}
		event.preventDefault();
		photos(links, selected, silverVars.lightbox);
	});

	Silver.themePopup = {
		open, photos,
		close: () => current?.dialog.close(),
		get isOpen() { return !!current; },
		get currItem() { return current?.item; },
		refresh() {
			if (current) Silver.attach(current.dialog.element);
		},
	};
})();
