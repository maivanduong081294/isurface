/**
 * Table of contents element ([ux_toc]...[/ux_toc], inc/shortcodes/ux_toc.php).
 *
 * The list and the heading ids come from PHP; this script:
 * - marks the section being read (scrollspy): the link, and its first-level
 *   item when the link is a second-level one;
 * - scrolls to a heading below the sticky header (and the mobile bar) on a
 *   click, then moves the focus to it;
 * - below 850px (.ux-toc-layout--mobile-bar): shows a bar fixed under the
 *   header while the wrapped content is in view, with the current section;
 *   it opens the same list.
 *
 * Registered as a Silver behavior so it also runs on content re-rendered by
 * UX Builder.
 */
(function ($) {
	'use strict';

	if (!window.Silver || typeof window.Silver.behavior !== 'function') {
		return;
	}

	var MOBILE = window.matchMedia('(max-width: 849px)');
	var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var tocs = [];
	var bar = null;
	var ticking = false;
	var listening = false;

	// Height covered at the top of the window: admin bar + stuck header. The
	// height, not the position: the header slides in when it gets stuck.
	function topOffset() {
		var offset = 0;
		var adminBar = document.getElementById('wpadminbar');
		var header = document.querySelector('#header .header-wrapper');

		if (adminBar && window.getComputedStyle(adminBar).position === 'fixed') {
			offset = adminBar.offsetHeight;
		}
		if (header && header.classList.contains('stuck')) {
			offset += header.offsetHeight;
		}
		return Math.max(0, Math.round(offset));
	}

	// Offset once the page has scrolled to a heading: a sticky header is
	// stuck by then even when it is not yet.
	function targetOffset() {
		var offset = topOffset();
		var header = document.getElementById('header');
		var wrapper = header && header.querySelector('.header-wrapper');

		// Not stuck yet: the rows it will keep once stuck. Not the
		// .hide-for-sticky ones (the theme hides them, like the top bar), nor
		// the absolutely positioned layers (.header-bg-container).
		if (wrapper && header.classList.contains('has-sticky') && !wrapper.classList.contains('stuck')) {
			Array.prototype.forEach.call(wrapper.children, function (row) {
				var position = window.getComputedStyle(row).position;
				if (!row.classList.contains('hide-for-sticky') && position !== 'absolute' && position !== 'fixed') {
					offset += row.offsetHeight;
				}
			});
		}
		if (bar && MOBILE.matches) {
			offset += bar.el.offsetHeight + 8;
		}
		return offset + 16;
	}

	// The collapsed Read more element ([ux_readmore]) a heading is in, if any.
	function collapsedReadmore(heading) {
		var readmore = heading.closest('.ux-readmore.is-collapsible:not(.is-expanded)');
		return readmore || null;
	}

	// Whether a heading is in the hidden part of a collapsed Read more.
	function isHidden(heading) {
		var readmore = collapsedReadmore(heading);
		var content = readmore && readmore.querySelector('.ux-readmore__content');

		return !!content && heading.getBoundingClientRect().top >= content.getBoundingClientRect().bottom - 1;
	}

	function scrollToHeading(heading) {
		// Opened at once, so the position below is the final one.
		if (collapsedReadmore(heading)) {
			$(collapsedReadmore(heading)).trigger('ux-readmore:expand');
		}

		var top = heading.getBoundingClientRect().top + window.pageYOffset - targetOffset();

		window.scrollTo({ top: Math.max(0, top), behavior: reduceMotion ? 'auto' : 'smooth' });

		if (!heading.hasAttribute('tabindex')) {
			heading.setAttribute('tabindex', '-1');
		}
		heading.focus({ preventScroll: true });

		try {
			window.history.replaceState(null, '', '#' + heading.id);
		} catch (e) {
			// A page served from another origin than its <base> refuses it.
		}
	}

	function headingOf(link) {
		var id = decodeURIComponent((link.getAttribute('href') || '').slice(1));
		return id ? document.getElementById(id) : null;
	}

	// Entries of a list: one per link, in order; a second-level entry points
	// to its first-level one.
	function readEntries(list) {
		var entries = [];
		var parent = null;

		Array.prototype.forEach.call(list.querySelectorAll('.ux-toc__link'), function (link) {
			var heading = headingOf(link);
			var item = link.parentNode;
			var isTop = item.classList.contains('ux-toc__item');
			var entry;

			if (!heading) return;

			if (isTop) {
				entry = {
					heading: heading,
					num: (link.querySelector('.ux-toc__num') || {}).textContent || '',
					text: ((link.querySelector('.ux-toc__text') || link).textContent || '').trim(),
					parent: null
				};
				parent = entry;
			} else {
				entry = { heading: heading, num: '', text: link.textContent.trim(), parent: parent };
			}
			entries.push(entry);
		});
		return entries;
	}

	// Marks the entry of index i (-1: none) in a list.
	function markList(list, entries, i) {
		var entry = entries[i];
		var topEntry = entry ? (entry.parent || entry) : null;
		var links = list.querySelectorAll('.ux-toc__link');

		Array.prototype.forEach.call(links, function (link) {
			var heading = headingOf(link);
			var on = !!entry && heading === entry.heading;
			var item = link.parentNode;

			link.classList.toggle('is-active', on);
			if (on) {
				link.setAttribute('aria-current', 'true');
			} else {
				link.removeAttribute('aria-current');
			}
			if (item.classList.contains('ux-toc__item')) {
				item.classList.toggle('is-active', !!topEntry && heading === topEntry.heading);
			}
		});
	}

	function currentIndex(toc) {
		var line = topOffset() + (bar && MOBILE.matches ? bar.el.offsetHeight + 8 : 0) + 24;
		var index = -1;

		for (var i = 0; i < toc.entries.length; i++) {
			// Hidden by a collapsed Read more: not reached, nor the ones after.
			if (isHidden(toc.entries[i].heading)) {
				break;
			}
			if (toc.entries[i].heading.getBoundingClientRect().top - line <= 1) {
				index = i;
			} else {
				break;
			}
		}

		// Bottom of the page: the last headings can never reach the line.
		if (window.innerHeight + window.pageYOffset >= document.documentElement.scrollHeight - 2) {
			for (var j = toc.entries.length - 1; j > index; j--) {
				if (!isHidden(toc.entries[j].heading) && toc.entries[j].heading.getBoundingClientRect().top < window.innerHeight) {
					index = j;
					break;
				}
			}
		}
		return index;
	}

	/* --------------------------------------------------------- Mobile bar */

	function barLabel(toc, index) {
		var entry = toc.entries[index >= 0 ? index : 0];
		var num = function (e) { return toc.numbered && e.num ? e.num + ' ' : ''; };

		if (entry.parent) {
			return num(entry.parent) + entry.parent.text + ' · ' + entry.text;
		}
		return num(entry) + entry.text;
	}

	function setBarOpen(open) {
		bar.open = open;
		bar.el.classList.toggle('is-open', open);
		bar.toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		bar.panel.hidden = !open;
	}

	function createBar(toc) {
		var title = toc.nav.getAttribute('data-title') || toc.nav.getAttribute('aria-label') || '';
		var panelId = toc.layout.id + '-bar';
		var el = document.createElement('div');

		el.className = 'ux-toc-bar is-ready' + (toc.numbered ? '' : ' no-numbers') +
			(toc.nav.classList.contains('ux-toc--subs-active') ? ' ux-toc--subs-active' : '');
		el.setAttribute('inert', '');
		el.innerHTML =
			'<button type="button" class="ux-toc-bar__toggle" aria-expanded="false" aria-controls="' + panelId + '">' +
				'<span class="ux-toc-bar__label"></span>' +
				'<span class="ux-toc-bar__current"></span>' +
				'<svg class="ux-toc-bar__icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" focusable="false"><path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>' +
			'</button>' +
			'<nav class="ux-toc-bar__panel" id="' + panelId + '" hidden></nav>';

		el.querySelector('.ux-toc-bar__label').textContent = title;
		el.querySelector('.ux-toc-bar__panel').setAttribute('aria-label', toc.nav.getAttribute('aria-label') || title);
		el.querySelector('.ux-toc-bar__panel').appendChild(toc.list.cloneNode(true));
		document.body.appendChild(el);

		bar = {
			el: el,
			toc: toc,
			toggle: el.querySelector('.ux-toc-bar__toggle'),
			current: el.querySelector('.ux-toc-bar__current'),
			panel: el.querySelector('.ux-toc-bar__panel'),
			open: false,
			visible: false
		};
		bar.current.textContent = barLabel(toc, -1);

		bar.toggle.addEventListener('click', function () {
			setBarOpen(!bar.open);
		});
		bar.panel.addEventListener('click', function (e) {
			var link = e.target.closest('a.ux-toc__link');
			var heading = link && headingOf(link);
			if (!heading) return;
			e.preventDefault();
			setBarOpen(false);
			scrollToHeading(heading);
		});
		document.addEventListener('click', function (e) {
			if (bar.open && !bar.el.contains(e.target)) setBarOpen(false);
		});
		document.addEventListener('keydown', function (e) {
			if (bar.open && e.key === 'Escape') {
				setBarOpen(false);
				bar.toggle.focus();
			}
		});
	}

	function updateBar() {
		if (!bar) return;

		var offset = topOffset();
		var rect = bar.toc.content.getBoundingClientRect();
		var visible = MOBILE.matches && rect.top < offset + 24 && rect.bottom > offset + 80;

		bar.el.style.top = (offset + 8) + 'px';
		if (visible === bar.visible) return;

		bar.visible = visible;
		bar.el.classList.toggle('is-visible', visible);
		if (visible) {
			bar.el.removeAttribute('inert');
		} else {
			bar.el.setAttribute('inert', '');
			setBarOpen(false);
		}
	}

	/* -------------------------------------------------------------- Update */

	function update() {
		ticking = false;

		var offset = topOffset();

		tocs.forEach(function (toc) {
			toc.nav.style.setProperty('--ux-toc-top', offset + 'px');

			var index = currentIndex(toc);
			if (index === toc.active) return;

			toc.active = index;
			markList(toc.list, toc.entries, index);
			if (bar && bar.toc === toc) {
				markList(bar.panel.querySelector('.ux-toc__list'), toc.entries, index);
				bar.current.textContent = barLabel(toc, index);
			}
		});
		updateBar();
	}

	function requestUpdate() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(update);
		}
	}

	function listen() {
		if (listening) return;
		listening = true;

		window.addEventListener('scroll', requestUpdate, { passive: true });
		window.addEventListener('resize', requestUpdate);
		// A Read more element opened or closed ([ux_readmore]).
		$(document).on('ux-readmore:toggle', requestUpdate);
		if (MOBILE.addEventListener) {
			MOBILE.addEventListener('change', requestUpdate);
		}

		// The header gets / loses "stuck" in its own scroll handler, which may
		// run after this one.
		var header = document.querySelector('#header .header-wrapper');
		if (header && window.MutationObserver) {
			new MutationObserver(requestUpdate).observe(header, { attributes: true, attributeFilter: ['class'] });
		}
	}

	window.Silver.behavior('ux-toc', {
		attach: function (context) {
			$(context || document).find('.ux-toc-layout').addBack('.ux-toc-layout').each(function () {
				var nav = this.querySelector('.ux-toc');
				var list = nav && nav.querySelector('.ux-toc__list');
				var content = this.querySelector('.ux-toc-layout__content');

				if (!list || !content || $(this).data('uxTocReady')) return;
				$(this).data('uxTocReady', true);

				var toc = {
					layout: this,
					nav: nav,
					list: list,
					content: content,
					entries: readEntries(list),
					numbered: !nav.classList.contains('no-numbers'),
					active: null
				};
				if (!toc.entries.length) return;

				// The second level may now follow the section being read
				// (.ux-toc--subs-active); without the script it stays open.
				nav.classList.add('is-ready');
				tocs.push(toc);
				list.addEventListener('click', function (e) {
					var link = e.target.closest('a.ux-toc__link');
					var heading = link && headingOf(link);
					if (!heading) return;
					e.preventDefault();
					scrollToHeading(heading);
				});

				if (!bar && this.classList.contains('ux-toc-layout--mobile-bar')) {
					createBar(toc);
				}

				listen();
				requestUpdate();
			});
		}
	});
})(jQuery);
