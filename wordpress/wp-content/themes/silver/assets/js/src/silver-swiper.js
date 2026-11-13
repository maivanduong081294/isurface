/**
 * Silver slider: Swiper engine for every slider in the theme.
 *
 * What it does:
 * - Builds the Swiper markup around the slides and maps the slider options
 *   declared by shortcodes/templates (data-swiper-options, built in PHP by
 *   silver_slider_options(): cellAlign, wrapAround, autoPlay,
 *   prevNextButtons, pageDots, ...) to Swiper options.
 * - Reads class modifiers on the slider element:
 *     slider-loop                  infinite loop
 *     slider-auto-3000             autoplay every 3000ms
 *     slider-no-nav                no prev/next buttons
 *     slider-no-bullet             no pagination bullets
 *     slider-vertical              vertical direction
 *     slider-vertical-{tier}       vertical from that tier up (medium|large|xlarge)
 *     slider-horizontal-{tier}     horizontal from that tier up
 *     slider-bullet-{key}          main slider controlled by a thumbnail slider
 *     slider-thumbnail-{key}       thumbnail slider for "slider-bullet-{key}"
 * - Marks up the slider with the theme's own `swiper-*` classes (swiper-enabled,
 *   swiper-viewport, swiper-slider, swiper-prev-next-button, swiper-page-dots),
 *   which the theme CSS styles.
 * - Keeps the jQuery `.flickity()` API and the `*.flickity` events that the
 *   compiled theme scripts call (product gallery, quick view, variation
 *   images), so those bundles keep working unchanged.
 * - Loads the Swiper library on demand from `silver.swiper`.
 */
(function ($, window, document) {
	'use strict';

	if (!$) return;

	const config = window.silver || {};
	const DATA_KEY = 'flickity';
	const EVENT_NS = '.flickity';
	const OPTIONS_ATTR = 'data-swiper-options';
	const LEGACY_OPTIONS_ATTR = 'data-flickity-options';
	const BREAKPOINTS = { medium: 550, large: 850, xlarge: 1200 };
	const DEFAULT_SPEED = 400;
	const DEFAULT_AUTOPLAY = 3000;
	const ARROW_PATH = 'M 10,50 L 60,100 L 70,90 L 30,50  L 70,10 L 60,0 Z';

	// Flickity-style defaults, so declared options keep their meaning.
	const DEFAULTS = {
		cellAlign: 'center',
		wrapAround: false,
		rewind: false,
		autoPlay: false,
		pauseAutoPlayOnHover: true,
		prevNextButtons: true,
		pageDots: true,
		draggable: true,
		freeScroll: false,
		adaptiveHeight: false,
		contain: false,
		groupCells: false,
		initialIndex: 0,
		rightToLeft: false,
		dragThreshold: 3,
		direction: 'horizontal',
		fade: false,
		asNavFor: null,
		breakpoints: null,
	};

	let uid = 0;

	/* ------------------------------------------------------------------ */
	/* Library loader                                                       */
	/* ------------------------------------------------------------------ */

	let libraryPromise = null;

	function loadSwiper() {
		if (window.Swiper) return Promise.resolve(window.Swiper);

		if (!libraryPromise) {
			libraryPromise = new Promise((resolve, reject) => {
				if (!config.swiper) {
					reject(new Error('Silver slider: "silver.swiper" is not defined.'));
					return;
				}

				const script = document.createElement('script');
				script.src = config.swiper;
				script.async = true;
				script.onload = () =>
					window.Swiper
						? resolve(window.Swiper)
						: reject(new Error('Silver slider: Swiper did not register.'));
				script.onerror = () =>
					reject(new Error('Không tải được Swiper: ' + config.swiper));
				document.head.appendChild(script);
			});

			libraryPromise.catch((error) => console.error(error.message));
		}

		return libraryPromise;
	}

	/* ------------------------------------------------------------------ */
	/* Options                                                              */
	/* ------------------------------------------------------------------ */

	// Value of the first class with the prefix: "slider-bullet-main" -> "main".
	const classValue = (classes, prefix) => {
		const found = classes.find((name) => name.startsWith(prefix));
		return found ? found.slice(prefix.length) : null;
	};

	// Options declared through class modifiers on the slider element.
	function readClassOptions(element) {
		const classes = Array.from(element.classList);
		const has = (name) => element.classList.contains(name);
		const options = {};

		if (has('slider-vertical')) options.direction = 'vertical';
		if (has('slider-loop')) options.wrapAround = true;
		if (has('slider-no-nav')) options.prevNextButtons = false;
		if (has('slider-no-bullet')) options.pageDots = false;
		if (has('slider-auto-height')) options.adaptiveHeight = true;
		if (has('slider-type-fade')) options.fade = true;

		// Flickity moved slides in the DOM only virtually. Galleries that other
		// scripts read in DOM order must not be reordered by Swiper's loop.
		if (has('product-gallery-slider')) options.rewind = true;

		// Autoplay: slider-auto-3000.
		const auto = classes
			.map((name) => /^slider-auto-(\d+)$/.exec(name))
			.find(Boolean);
		if (auto && parseInt(auto[1], 10) > 1) options.autoPlay = parseInt(auto[1], 10);

		// Direction per tier. Swiper applies only the largest matching breakpoint,
		// so a direction set on one tier is carried to the tiers above it.
		const breakpoints = {};
		let direction = null;
		Object.keys(BREAKPOINTS).forEach((tier) => {
			if (has('slider-vertical-' + tier)) direction = 'vertical';
			if (has('slider-horizontal-' + tier)) direction = 'horizontal';
			if (direction) breakpoints[BREAKPOINTS[tier]] = { direction };
		});
		if (Object.keys(breakpoints).length) options.breakpoints = breakpoints;

		// Main slider + thumbnails: slider-bullet-{key} / slider-thumbnail-{key}.
		if (classValue(classes, 'slider-bullet-')) options.pageDots = false;
		const thumbKey = classValue(classes, 'slider-thumbnail-');
		if (thumbKey) {
			options.asNavFor = '.slider-bullet-' + thumbKey;
			// A thumbnail follows the main slider: it must keep its slides in
			// DOM order (no loop) and never move on its own.
			options.wrapAround = false;
			options.autoPlay = false;
			options.pageDots = false;
		}

		return options;
	}

	// Declared options + class modifiers (class modifiers win).
	function normalizeSettings(options, element) {
		return Object.assign({}, DEFAULTS, options || {}, readClassOptions(element));
	}

	// Map the normalized settings to Swiper parameters.
	function toSwiperOptions(settings, slideCount, refs) {
		const wantsLoop = !!settings.wrapAround && slideCount > 1;
		// Swiper needs spare slides to loop: more than what is visible plus one
		// group. Otherwise fall back to rewind (jump back to the first slide).
		const perView = Math.max(1, refs.perView || 1);
		const perGroup = settings.groupCells ? perView : 1;
		const useLoop =
			wantsLoop && !settings.rewind && slideCount >= 3 && slideCount >= perView + perGroup + 1;
		const centered = settings.cellAlign === 'center';
		const reducedMotion =
			Number(settings.friction) === 1 && Number(settings.selectedAttraction) === 1;

		const options = {
			slidesPerView: 'auto',
			spaceBetween: 0,
			direction: settings.direction === 'vertical' ? 'vertical' : 'horizontal',
			speed: reducedMotion ? 0 : DEFAULT_SPEED,
			initialSlide: parseInt(settings.initialIndex, 10) || 0,
			centeredSlides: centered,
			centeredSlidesBounds: centered && !!settings.contain && !useLoop,
			loop: useLoop,
			rewind: wantsLoop && !useLoop,
			allowTouchMove: settings.draggable !== false && slideCount > 1,
			threshold: Number(settings.dragThreshold) || 0,
			autoHeight: !!settings.adaptiveHeight,
			watchSlidesProgress: true,
		};

		if (settings.groupCells) {
			const group = parseInt(settings.groupCells, 10);
			if (settings.groupCells === true || String(settings.groupCells).indexOf('%') > -1 || !(group > 1)) {
				options.slidesPerGroupAuto = true;
			} else {
				options.slidesPerGroup = group;
			}
		}

		if (settings.freeScroll) {
			options.freeMode = { enabled: true, sticky: false };
		}

		if (settings.fade) {
			options.effect = 'fade';
			options.fadeEffect = { crossFade: true };
			options.slidesPerView = 1;
			options.centeredSlides = false;
			options.centeredSlidesBounds = false;
		}

		if (settings.autoPlay) {
			const delay = settings.autoPlay === true ? DEFAULT_AUTOPLAY : parseInt(settings.autoPlay, 10);
			if (delay > 1) {
				options.autoplay = {
					delay,
					disableOnInteraction: false,
					pauseOnMouseEnter: settings.pauseAutoPlayOnHover !== false,
				};
			}
		}

		if (refs.prevButton && refs.nextButton) {
			options.navigation = { prevEl: refs.prevButton, nextEl: refs.nextButton };
		}

		if (refs.dots) {
			options.pagination = {
				el: refs.dots,
				clickable: true,
				bulletElement: 'li',
				bulletClass: 'dot',
				bulletActiveClass: 'is-selected',
			};
		}

		if (settings.breakpoints) {
			options.breakpoints = settings.breakpoints;
		}

		return options;
	}

	/* ------------------------------------------------------------------ */
	/* DOM helpers                                                          */
	/* ------------------------------------------------------------------ */

	const isCell = (node) => !/^(STYLE|SCRIPT|LINK|TEMPLATE)$/.test(node.tagName);

	function createArrow(direction, label) {
		const svgNS = 'http://www.w3.org/2000/svg';
		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'swiper-button swiper-prev-next-button ' + direction;
		button.setAttribute('aria-label', label);

		const svg = document.createElementNS(svgNS, 'svg');
		svg.setAttribute('class', 'swiper-button-icon');
		svg.setAttribute('viewBox', '0 0 100 100');

		const path = document.createElementNS(svgNS, 'path');
		path.setAttribute('d', ARROW_PATH);
		path.setAttribute('class', 'arrow');
		if (direction === 'next') {
			path.setAttribute('transform', 'translate(100, 100) rotate(180) ');
		}

		svg.appendChild(path);
		button.appendChild(svg);
		return button;
	}

	/* ------------------------------------------------------------------ */
	/* Slider instance                                                      */
	/* ------------------------------------------------------------------ */

	class SilverSlider {
		constructor(element, options) {
			this.element = element;
			this.$element = $(element);
			this.options = Object.assign({}, options);
			this.uid = ++uid;
			this.swiper = null;
			this.isReady = false;
			this.isDestroyed = false;
			this.isDragging = false;
			this.selectedIndex = 0;
			this.queue = [];
			this.listeners = {};
			this.resizeTimer = null;

			this.build();
			$.data(element, DATA_KEY, this);

			loadSwiper()
				.then((Swiper) => this.mount(Swiper))
				.catch(() => {
					// Without the library the slides stay reachable by scrolling.
					if (this.viewport) this.viewport.style.overflowX = 'auto';
				});
		}

		/* ---- build / mount ---- */

		// Wrap the slides in the Swiper structure. The theme CSS and scripts
		// target these class names:
		//   element.swiper-enabled > .swiper-viewport.swiper
		//     > .swiper-slider.swiper-wrapper > .swiper-slide
		build() {
			const element = this.element;
			const settings = (this.settings = normalizeSettings(this.options, element));
			const cells = Array.from(element.children).filter(isCell);

			this.viewport = document.createElement('div');
			this.viewport.className = 'swiper-viewport swiper';
			this.slider = document.createElement('div');
			this.slider.className = 'swiper-slider swiper-wrapper';

			cells.forEach((cell) => {
				cell.classList.add('swiper-slide');
				this.slider.appendChild(cell);
			});

			this.viewport.appendChild(this.slider);
			element.insertBefore(this.viewport, element.firstChild);
			element.classList.add('swiper-enabled', 'swiper-section');

			if (settings.rightToLeft) {
				this.viewport.setAttribute('dir', 'rtl');
				element.classList.add('swiper-rtl');
			}

			if (settings.draggable !== false && cells.length > 1) {
				element.classList.add('is-draggable');
			}

			if (settings.prevNextButtons) {
				this.prevButton = createArrow('previous', 'Previous');
				this.nextButton = createArrow('next', 'Next');
				element.appendChild(this.prevButton);
				element.appendChild(this.nextButton);
			}

			if (settings.pageDots) {
				this.dots = document.createElement('ol');
				this.dots.className = 'swiper-page-dots';
				element.appendChild(this.dots);
			}

			this.selectedIndex = Math.min(
				Math.max(parseInt(settings.initialIndex, 10) || 0, 0),
				Math.max(cells.length - 1, 0)
			);
			if (cells[this.selectedIndex]) cells[this.selectedIndex].classList.add('is-selected');

			// Images change the slide sizes once they load.
			this.onImageLoad = (event) => {
				if (event.target && event.target.tagName === 'IMG') this.scheduleResize();
			};
			element.addEventListener('load', this.onImageLoad, true);

			this.bindNav();
		}

		mount(Swiper) {
			if (this.isDestroyed) return;

			const options = toSwiperOptions(this.settings, this.getCellElements().length, {
				perView: this.countVisibleSlides(),
				prevButton: this.prevButton,
				nextButton: this.nextButton,
				dots: this.dots,
			});

			const swiper = (this.swiper = new Swiper(this.viewport, options));

			swiper.on('slideChange', () => {
				this.syncSelected();
				this.emit('select', [this.selectedIndex]);
				this.emit('change', [this.selectedIndex]);
			});
			swiper.on('transitionEnd', () => this.emit('settle', [this.selectedIndex]));
			swiper.on('touchStart', () => this.viewport.classList.add('is-pointer-down'));
			swiper.on('sliderFirstMove', (instance, event) => {
				this.isDragging = true;
				this.emit('dragStart', [event]);
			});
			swiper.on('touchEnd', (instance, event) => {
				this.viewport.classList.remove('is-pointer-down');
				if (this.isDragging) {
					this.isDragging = false;
					this.emit('dragEnd', [event]);
				}
			});
			swiper.on('setTranslate', (instance, translate) => {
				this.emit('scroll', [instance.progress, translate]);
			});
			swiper.on('click', (instance, event) => {
				const cell = instance.clickedSlide;
				const index = cell ? this.getCellElements().indexOf(cell) : undefined;
				this.emit('staticClick', [event, event, cell, index]);
			});

			this.isReady = true;
			this.syncSelected();

			const queue = this.queue;
			this.queue = [];
			queue.forEach((task) => task());

			this.emit('ready');
		}

		// Run now, or as soon as Swiper is mounted.
		run(task) {
			if (this.isDestroyed) return;
			if (this.isReady) task();
			else this.queue.push(task);
		}

		/* ---- state ---- */

		// How many slides fit in the viewport right now (at least 1).
		countVisibleSlides() {
			const first = this.getCellElements()[0];
			const vertical = this.settings.direction === 'vertical';
			const viewportSize = vertical ? this.viewport.clientHeight : this.viewport.clientWidth;
			const slideSize = first ? (vertical ? first.offsetHeight : first.offsetWidth) : 0;
			if (!viewportSize || !slideSize) return 1;
			return Math.max(1, Math.round(viewportSize / slideSize));
		}

		getCellElements() {
			return this.slider ? Array.from(this.slider.children) : [];
		}

		get selectedElement() {
			if (this.swiper) return this.swiper.slides[this.swiper.activeIndex] || null;
			return this.getCellElements()[this.selectedIndex] || null;
		}

		// Flickity-compatible position data used by the parallax effect.
		get x() {
			return this.swiper ? this.swiper.translate : 0;
		}

		get slides() {
			const grid = this.swiper ? this.swiper.slidesGrid : [];
			return this.getCellElements().map((cell, index) => ({
				target: grid[index] || 0,
				cells: [{ element: cell }],
			}));
		}

		syncSelected() {
			const swiper = this.swiper;
			if (!swiper) return;

			this.selectedIndex = swiper.realIndex || 0;

			// With grouping every visible slide of the group counts as selected.
			let groupSize = 1;
			if (this.settings.groupCells && typeof swiper.slidesPerViewDynamic === 'function') {
				groupSize = Math.max(1, Math.floor(swiper.slidesPerViewDynamic()) || 1);
			}

			const slides = Array.from(swiper.slides);
			slides.forEach((slide) => slide.classList.remove('is-selected'));
			for (let i = 0; i < groupSize; i++) {
				const slide = slides[swiper.activeIndex + i];
				if (slide) slide.classList.add('is-selected');
			}
		}

		scheduleResize() {
			clearTimeout(this.resizeTimer);
			this.resizeTimer = setTimeout(() => this.resize(), 100);
		}

		/* ---- events ---- */

		on(name, listener) {
			(this.listeners[name] = this.listeners[name] || []).push(listener);
			return this;
		}

		off(name, listener) {
			const list = this.listeners[name];
			if (list) this.listeners[name] = list.filter((item) => item !== listener);
			return this;
		}

		once(name, listener) {
			const wrapped = (...args) => {
				this.off(name, wrapped);
				listener.apply(this, args);
			};
			return this.on(name, wrapped);
		}

		emit(name, args) {
			const list = (this.listeners[name] || []).slice();
			list.forEach((listener) => listener.apply(this, args || []));
			this.$element.trigger(name + EVENT_NS, args || []);
		}

		/* ---- thumbnails (asNavFor) ---- */

		getNavTarget() {
			const selector = this.settings.asNavFor;
			if (!selector) return $();
			if (typeof selector !== 'string') return $(selector).first();

			// Prefer the gallery in the same product / lightbox.
			const $scope = this.$element.closest('.product, .product-lightbox, .mfp-content');
			const $scoped = $scope.length ? $scope.find(selector).first() : $();
			return $scoped.length ? $scoped : $(selector).first();
		}

		bindNav() {
			if (!this.settings.asNavFor) return;

			const namespace = '.silverNav' + this.uid;
			const $target = (this.$navTarget = this.getNavTarget());

			// Highlight the thumbnail of the slide the main slider starts on,
			// unless the markup already marks one.
			const cells = this.getCellElements();
			if (!cells.some((cell) => cell.classList.contains('is-nav-selected'))) {
				const main = $target.data(DATA_KEY);
				const current = cells[main ? main.selectedIndex : 0] || cells[0];
				if (current) current.classList.add('is-nav-selected');
			}

			// Clicking a thumbnail selects the slide in the main slider.
			this.$element.on('click' + namespace, '.swiper-slider > *', (event) => {
				const index = this.getCellElements().indexOf(event.currentTarget);
				const target = this.getNavTarget().data(DATA_KEY);
				if (target && index > -1) target.select(index);
			});

			// The main slider selecting a slide highlights its thumbnail.
			$target.on('select' + EVENT_NS + namespace, (event, index) => {
				if (event.target !== $target[0]) return;
				this.setNavSelected(index);
			});
		}

		setNavSelected(index) {
			const cells = this.getCellElements();
			cells.forEach((cell) => cell.classList.remove('is-nav-selected'));
			if (!cells[index]) return;
			cells[index].classList.add('is-nav-selected');
			this.select(index);
		}

		/* ---- public API (Flickity-compatible) ---- */

		select(index, isWrap, isInstant) {
			this.run(() => {
				const swiper = this.swiper;
				const target = parseInt(index, 10) || 0;
				const speed = isInstant ? 0 : undefined;
				if (swiper.params.loop) swiper.slideToLoop(target, speed);
				else swiper.slideTo(target, speed);
			});
			return this;
		}

		selectCell(value, isWrap, isInstant) {
			let index = value;
			if (typeof value !== 'number') {
				const cell = typeof value === 'string' ? this.slider.querySelector(value) : value;
				index = this.getCellElements().indexOf(cell);
			}
			return index > -1 ? this.select(index, isWrap, isInstant) : this;
		}

		next() {
			this.run(() => this.swiper.slideNext());
			return this;
		}

		previous() {
			this.run(() => this.swiper.slidePrev());
			return this;
		}

		resize() {
			this.run(() => {
				this.swiper.update();
				if (this.swiper.params.autoHeight) this.swiper.updateAutoHeight(0);
				this.syncSelected();
			});
			return this;
		}

		reposition() {
			return this.resize();
		}

		reloadCells() {
			return this.resize();
		}

		// Change the slide list. Works before Swiper is mounted as well.
		mutate(change) {
			const swiper = this.swiper;
			const looped = swiper && swiper.params.loop;

			if (looped) swiper.loopDestroy();
			change();
			if (looped) swiper.loopCreate();
			if (swiper) {
				if (typeof swiper.recalcSlides === 'function') swiper.recalcSlides();
				swiper.update();
				// Stay on a slide that still exists.
				const last = Math.max(swiper.slides.length - 1, 0);
				if (swiper.activeIndex > last) swiper.slideTo(last, 0, false);
				this.syncSelected();
			}
			return this;
		}

		insert(elements, index) {
			const nodes = $(elements).toArray();
			return this.mutate(() => {
				const before = typeof index === 'number' ? this.slider.children[index] || null : null;
				nodes.forEach((node) => {
					node.classList.add('swiper-slide');
					this.slider.insertBefore(node, before);
				});
			});
		}

		append(elements) {
			return this.insert(elements);
		}

		prepend(elements) {
			return this.insert(elements, 0);
		}

		remove(elements) {
			const nodes = $(elements).toArray();
			return this.mutate(() => {
				nodes.forEach((node) => {
					if (node.parentNode === this.slider) this.slider.removeChild(node);
				});
			});
		}

		playPlayer() {
			this.run(() => this.swiper.autoplay && this.swiper.params.autoplay.enabled && this.swiper.autoplay.start());
			return this;
		}

		stopPlayer() {
			this.run(() => this.swiper.autoplay && this.swiper.autoplay.stop());
			return this;
		}

		pausePlayer() {
			this.run(() => this.swiper.autoplay && this.swiper.autoplay.pause());
			return this;
		}

		unpausePlayer() {
			this.run(() => this.swiper.autoplay && this.swiper.autoplay.resume());
			return this;
		}

		// Calling .flickity(options) on an existing slider re-creates it.
		option(options) {
			const element = this.element;
			const merged = Object.assign({}, this.options, options);
			this.destroy();
			return new SilverSlider(element, merged);
		}

		destroy() {
			if (this.isDestroyed) return;
			this.isDestroyed = true;
			this.queue = [];
			clearTimeout(this.resizeTimer);

			const element = this.element;

			if (this.swiper) {
				this.swiper.destroy(true, true);
				this.swiper = null;
			}

			// Put the slides back where they were.
			this.getCellElements().forEach((cell) => {
				cell.classList.remove('swiper-slide', 'is-selected');
				cell.removeAttribute('data-swiper-slide-index');
				element.insertBefore(cell, this.viewport);
			});

			[this.viewport, this.prevButton, this.nextButton, this.dots].forEach((node) => {
				if (node && node.parentNode) node.parentNode.removeChild(node);
			});

			element.classList.remove('swiper-enabled', 'swiper-section', 'is-draggable', 'swiper-rtl');
			element.removeEventListener('load', this.onImageLoad, true);

			const namespace = '.silverNav' + this.uid;
			this.$element.off(namespace);
			if (this.$navTarget) this.$navTarget.off(namespace);

			this.emit('destroy');
			this.listeners = {};
			$.removeData(element, DATA_KEY);
		}
	}

	/* ------------------------------------------------------------------ */
	/* jQuery plugin                                                        */
	/* ------------------------------------------------------------------ */

	// $(el).flickity(options)      create (or re-create) a slider
	// $(el).flickity('select', 2)  call a method on an existing slider
	function plugin(arg, ...args) {
		if (typeof arg === 'string') {
			let result;
			this.each((index, element) => {
				const instance = $.data(element, DATA_KEY);
				if (!instance || typeof instance[arg] !== 'function' || arg.charAt(0) === '_') return;
				const value = instance[arg](...args);
				if (result === undefined && value !== undefined && value !== instance) result = value;
			});
			return result !== undefined ? result : this;
		}

		return this.each((index, element) => {
			const instance = $.data(element, DATA_KEY);
			if (instance) instance.option(arg);
			else new SilverSlider(element, arg);
		});
	}

	plugin.isSwiper = true;
	$.fn.flickity = plugin;
	$.fn.silverSlider = plugin;

	// Markup saved before the attribute was renamed (hand-written HTML, cached
	// fragments) still declares its options in "data-flickity-options".
	function upgradeLegacyMarkup(context) {
		$('[' + LEGACY_OPTIONS_ATTR + ']', context || document).each((index, element) => {
			if (!element.hasAttribute(OPTIONS_ATTR)) {
				element.setAttribute(OPTIONS_ATTR, element.getAttribute(LEGACY_OPTIONS_ATTR));
			}
			element.removeAttribute(LEGACY_OPTIONS_ATTR);
		});
	}

	// This script runs before the theme attaches its slider behavior.
	upgradeLegacyMarkup(document);

	// Sliders written by hand with class modifiers only (no declared options).
	function initStandalone(context) {
		$('.slider', context || document)
			.not('[' + OPTIONS_ATTR + ']')
			.not('.swiper-enabled')
			.not('.product-lightbox .slider')
			.each((index, element) => {
				if (Array.from(element.children).some(isCell)) $(element).flickity({});
			});
	}

	$(() => initStandalone(document));

	window.SilverSlider = {
		Slider: SilverSlider,
		load: loadSwiper,
		init: initStandalone,
		toSwiperOptions,
		readClassOptions,
	};
})(window.jQuery, window, document);
