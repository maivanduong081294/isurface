/**
 * Counter element ([ux_counter]).
 *
 * Every number of the value counts up from 0 when the counter scrolls into
 * view; the rest of the text is kept ("≤5 min", "2-3 days", "7/7"...).
 * Number formats kept: thousands separators ("1,250", "1.250"), decimals
 * ("4.8", "4,8"), leading zeros ("07").
 *
 * The final value is already in the HTML: nothing changes without JS or with
 * "reduce motion".
 */
(function ($) {
	'use strict';

	if (!window.Silver || typeof window.Silver.behavior !== 'function') {
		return;
	}

	var NUMBER = /\d+(?:[.,]\d+)*/g;
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Number token -> value + format.
	function parseNumber(token) {
		var m;

		// 1,250 / 1.250 (thousands only).
		if ((m = token.match(/^\d{1,3}(([.,])\d{3})+$/))) {
			return { value: parseInt(token.replace(/[.,]/g, ''), 10), decimals: 0, group: m[2], point: '', pad: 0 };
		}
		// 1,250.5 / 1.250,5 (thousands + decimals).
		if ((m = token.match(/^(\d{1,3}(?:([.,])\d{3})+)([.,])(\d+)$/)) && m[2] !== m[3]) {
			return { value: parseFloat(m[1].replace(/[.,]/g, '') + '.' + m[4]), decimals: m[4].length, group: m[2], point: m[3], pad: 0 };
		}
		// 4.8 / 4,8 (decimals).
		if ((m = token.match(/^(\d+)([.,])(\d+)$/))) {
			return { value: parseFloat(m[1] + '.' + m[3]), decimals: m[3].length, group: '', point: m[2], pad: 0 };
		}
		// 5 / 07.
		if (/^\d+$/.test(token)) {
			return { value: parseInt(token, 10), decimals: 0, group: '', point: '', pad: token.charAt(0) === '0' ? token.length : 0 };
		}
		return null;
	}

	function formatNumber(number, info) {
		var parts = number.toFixed(info.decimals).split('.');
		var integer = parts[0];

		while (integer.length < info.pad) {
			integer = '0' + integer;
		}
		if (info.group) {
			integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, info.group);
		}

		return parts.length > 1 ? integer + info.point + parts[1] : integer;
	}

	function easeOutCubic(t) {
		return 1 - Math.pow(1 - t, 3);
	}

	function prepare(el) {
		var text = el.getAttribute('data-value') || el.textContent;
		var pieces = [];
		var last = 0;
		var match;

		NUMBER.lastIndex = 0;
		while ((match = NUMBER.exec(text))) {
			var info = parseNumber(match[0]);
			if (!info) continue;
			pieces.push(text.slice(last, match.index));
			pieces.push(info);
			last = match.index + match[0].length;
		}
		pieces.push(text.slice(last));

		return pieces;
	}

	function render(el, pieces, progress) {
		var out = '';
		for (var i = 0; i < pieces.length; i++) {
			out += typeof pieces[i] === 'string'
				? pieces[i]
				: formatNumber(pieces[i].value * progress, pieces[i]);
		}
		el.textContent = out;
	}

	function run(el, pieces) {
		var duration = Math.max(200, (parseFloat(el.getAttribute('data-duration')) || 2) * 1000);
		var start = null;

		function frame(time) {
			if (start === null) start = time;
			var t = Math.min(1, (time - start) / duration);
			render(el, pieces, t < 1 ? easeOutCubic(t) : 1);
			if (t < 1) window.requestAnimationFrame(frame);
		}

		window.requestAnimationFrame(frame);
	}

	var observer = 'IntersectionObserver' in window
		? new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (!entry.isIntersecting) return;
				observer.unobserve(entry.target);
				run(entry.target, $(entry.target).data('uxCounterPieces'));
			});
		}, { threshold: 0.3 })
		: null;

	window.Silver.behavior('ux-counter', {
		attach: function (context) {
			if (reduceMotion) return;

			$(context || document).find('.ux-counter-number').addBack('.ux-counter-number').each(function () {
				var $el = $(this);
				if ($el.data('uxCounterPieces')) return;

				var pieces = prepare(this);
				$el.data('uxCounterPieces', pieces);

				// Nothing to count (no number in the value).
				if (pieces.length < 2) return;

				if (!observer) {
					run(this, pieces);
					return;
				}
				render(this, pieces, 0); // Start from 0 until visible.
				observer.observe(this);
			});
		}
	});
})(jQuery);
