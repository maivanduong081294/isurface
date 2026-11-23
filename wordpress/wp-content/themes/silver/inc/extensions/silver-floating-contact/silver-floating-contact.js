/* Silver Floating Contact: hide / show toggle and lists for channels with several values. */
(function () {
	var root = document.querySelector('[data-silver-fc]');
	if (!root) return;

	/* Heights of the bottom bar (style 1, phones) and of the sticky add to
	   cart bar of the product page: on phones this bar sits right above the
	   add to cart bar while it shows (silver-modern.css, --sfc-bar-h,
	   --sv-sticky-cart-h). */
	if ('ResizeObserver' in window) {
		var setHeight = function (name, el) {
			if (el.offsetHeight > 0) document.body.style.setProperty(name, el.offsetHeight + 'px');
		};
		var bar = root.querySelector('.silver-fc__list');
		var cart = document.querySelector('.sticky-add-to-cart');
		var heights = new ResizeObserver(function () {
			if (bar) setHeight('--sfc-bar-h', bar);
			if (cart) setHeight('--sv-sticky-cart-h', cart);
		});
		if (bar) heights.observe(bar);
		if (cart) heights.observe(cart);
	}

	/* Style 2: corner button opens a stack of buttons */
	var launcher = root.querySelector('.silver-fc__launcher');
	if (launcher) {
		var panel = root.querySelector('.silver-fc__panel');

		var setOpen = function (open) {
			root.classList.toggle('is-open', open);
			launcher.setAttribute('aria-expanded', open ? 'true' : 'false');
			if ('inert' in panel) panel.inert = !open;
		};

		/* Keep the theme's back-to-top button above the corner button. */
		var placeBackToTop = function () {
			var top = launcher.getBoundingClientRect().top;
			if (top > 0) document.body.style.setProperty('--sfc-btt', Math.round(window.innerHeight - top + 12) + 'px');
		};

		setOpen(root.classList.contains('is-open'));
		placeBackToTop();
		window.addEventListener('resize', placeBackToTop);

		launcher.addEventListener('click', function (e) {
			e.stopPropagation();
			setOpen(!root.classList.contains('is-open'));
		});

		document.addEventListener('click', function (e) {
			if (root.classList.contains('is-open') && !root.contains(e.target)) setOpen(false);
		});

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && root.classList.contains('is-open')) {
				setOpen(false);
				launcher.focus();
			}
		});
		return;
	}

	var KEY = 'silver_fc_collapsed';
	var toggle = root.querySelector('.silver-fc__toggle');

	/* Lists (several hotlines, Zalo numbers, ...) */
	function closeSubs(except) {
		var open = root.querySelectorAll('.has-sub.is-open');
		for (var i = 0; i < open.length; i++) {
			if (open[i] === except) continue;
			open[i].classList.remove('is-open');
			open[i].querySelector('.silver-fc__sub').hidden = true;
			open[i].querySelector('.silver-fc__sub-toggle').setAttribute('aria-expanded', 'false');
		}
	}

	var subToggles = root.querySelectorAll('.silver-fc__sub-toggle');
	for (var i = 0; i < subToggles.length; i++) {
		subToggles[i].addEventListener('click', function (e) {
			e.stopPropagation();
			var item = this.parentNode;
			var opening = !item.classList.contains('is-open');
			closeSubs(item);
			item.classList.toggle('is-open', opening);
			item.querySelector('.silver-fc__sub').hidden = !opening;
			this.setAttribute('aria-expanded', opening ? 'true' : 'false');
		});
	}

	document.addEventListener('click', function (e) {
		if (!e.target.closest || !e.target.closest('.silver-fc__sub')) closeSubs();
	});

	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') closeSubs();
	});

	/* Hide / show */
	if (!toggle) return;

	function set(collapsed) {
		if (collapsed) closeSubs();
		root.classList.toggle('is-collapsed', collapsed);
		document.body.classList.toggle('sfc-collapsed', collapsed);
		toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
	}

	var saved = false;
	try { saved = window.localStorage.getItem(KEY) === '1'; } catch (e) {}
	set(saved);

	toggle.addEventListener('click', function () {
		var collapsed = !root.classList.contains('is-collapsed');
		set(collapsed);
		try { window.localStorage.setItem(KEY, collapsed ? '1' : '0'); } catch (e) {}
	});
})();
