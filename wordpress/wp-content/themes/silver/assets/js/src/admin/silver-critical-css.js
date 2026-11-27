/**
 * Critical CSS generator (Advanced > Performance > "Generate critical CSS").
 *
 * Opens the sample pages of the site (silverCritical.urls) in hidden frames
 * at each screen size, keeps the rules of the theme stylesheets that style an
 * element of the first screen (or hide one there: dropdowns, drawers), in the
 * order of the original file, and sends them to the server
 * (inc/functions/performance/critical-css.php). Runs in the admin's browser:
 * no Node, any hosting.
 */
(function () {
	'use strict';

	var config = window.silverCritical;
	if (!config) return;

	// Splits a selector list on the top-level commas.
	function splitSelectors(text) {
		var out = [];
		var depth = 0;
		var current = '';
		for (var i = 0; i < text.length; i++) {
			var ch = text[i];
			if (ch === '(' || ch === '[') depth++;
			if (ch === ')' || ch === ']') depth--;
			if (ch === ',' && depth === 0) {
				out.push(current.trim());
				current = '';
			} else {
				current += ch;
			}
		}
		if (current.trim()) out.push(current.trim());
		return out;
	}

	// The selector without what querySelectorAll can not match or what
	// depends on the visitor (pseudo-elements, hover, focus...).
	function matchable(selector) {
		var s = selector
			.replace(/::?(before|after|first-letter|first-line|placeholder|selection|marker|backdrop|-webkit-[a-z-]+|-moz-[a-z-]+|-ms-[a-z-]+)(\([^)]*\))?/gi, '')
			.replace(/:(hover|focus|focus-visible|focus-within|active|visited|target|checked|indeterminate|invalid|valid|disabled|enabled|read-only|placeholder-shown|autofill)\b/gi, '')
			.trim();
		return s === '' || /[>+~]$/.test(s) ? '*' : s;
	}

	// The rules of the handled stylesheets that apply to the first screen of
	// the document of a frame: { handle: { id: rule } }.
	function collect(win) {
		var doc = win.document;
		var fold = win.innerHeight;

		// An element of the first screen: its box starts above the fold. A
		// hidden element (no box) counts by its nearest ancestor with a box.
		function inFold(el) {
			for (var node = el; node && node.nodeType === 1; node = node.parentElement) {
				if (node === doc.documentElement || node === doc.body) return true;
				var r = node.getBoundingClientRect();
				if (r.width || r.height) return r.top < fold;
			}
			return false;
		}

		function matchesFold(selector) {
			var list;
			try {
				list = doc.querySelectorAll(matchable(selector));
			} catch (e) {
				return false;
			}
			for (var i = 0; i < list.length; i++) {
				if (inFold(list[i])) return true;
			}
			return false;
		}

		var result = {};
		Object.keys(config.sheets).forEach(function (handle) {
			var path = config.sheets[handle];
			var sheet = null;
			for (var i = 0; i < doc.styleSheets.length; i++) {
				var href = doc.styleSheets[i].href;
				if (href && href.split('?')[0].slice(-path.length) === path) {
					sheet = doc.styleSheets[i];
					break;
				}
			}
			if (!sheet) return;

			var kept = {};
			var rules;
			try {
				rules = sheet.cssRules;
			} catch (e) {
				return; // Other origin (CDN): can not be read.
			}

			(function walk(list, path, media) {
				for (var index = 0; index < list.length; index++) {
					var rule = list[index];
					var id = path.concat(index);
					if (rule instanceof win.CSSMediaRule) {
						if (win.matchMedia(rule.media.mediaText).matches) {
							walk(rule.cssRules, id, media.concat('@media ' + rule.media.mediaText));
						}
					} else if (rule instanceof win.CSSSupportsRule) {
						if (win.CSS.supports(rule.conditionText)) {
							walk(rule.cssRules, id, media.concat('@supports ' + rule.conditionText));
						}
					} else if (rule instanceof win.CSSStyleRule) {
						var selectors = splitSelectors(rule.selectorText).filter(matchesFold);
						if (selectors.length) {
							kept[id.join('.')] = { media: media, selectors: selectors, body: rule.style.cssText };
						}
					} else if (rule instanceof win.CSSFontFaceRule) {
						kept[id.join('.')] = { media: media, raw: rule.cssText };
					}
				}
			})(rules, [], []);

			result[handle] = kept;
		});
		return result;
	}

	// Opens a page in a hidden frame of this size; resolves with its window
	// once loaded (and a moment for fonts, sliders).
	function openFrame(url, viewport) {
		return new Promise(function (resolve, reject) {
			var frame = document.createElement('iframe');
			frame.setAttribute('aria-hidden', 'true');
			frame.style.cssText = 'position:fixed;left:-20000px;top:0;border:0;width:' + viewport.width + 'px;height:' + viewport.height + 'px;';
			var timer = setTimeout(function () {
				frame.remove();
				reject(new Error('Timeout: ' + url));
			}, 60000);
			frame.onload = function () {
				setTimeout(function () {
					clearTimeout(timer);
					try {
						resolve({ win: frame.contentWindow, frame: frame });
					} catch (e) {
						frame.remove();
						reject(e);
					}
				}, 1500);
			};
			frame.src = url + (url.indexOf('?') === -1 ? '?' : '&') + 'silver_critical=1';
			document.body.appendChild(frame);
		});
	}

	// Original order: ids are the rule positions (1.4.2 = rule 2 of rule 4 of rule 1).
	function byPosition(a, b) {
		var x = a.split('.').map(Number);
		var y = b.split('.').map(Number);
		for (var i = 0; i < Math.max(x.length, y.length); i++) {
			var xi = i < x.length ? x[i] : -1;
			var yi = i < y.length ? y[i] : -1;
			if (xi !== yi) return xi - yi;
		}
		return 0;
	}

	function serialize(rules) {
		var css = '';
		var open = [];
		Object.keys(rules).sort(byPosition).forEach(function (id) {
			var rule = rules[id];
			var same = 0;
			while (same < open.length && same < rule.media.length && open[same] === rule.media[same]) same++;
			css += new Array(open.length - same + 1).join('}');
			rule.media.slice(same).forEach(function (block) {
				css += block + '{';
			});
			open = rule.media;
			css += rule.raw ? rule.raw : rule.selectors.join(',') + '{' + rule.body + '}';
		});
		return css + new Array(open.length + 1).join('}');
	}

	function generate(button, status) {
		var merged = {};
		Object.keys(config.sheets).forEach(function (handle) {
			merged[handle] = {};
		});

		var jobs = [];
		config.viewports.forEach(function (viewport) {
			config.urls.forEach(function (url) {
				jobs.push({ url: url, viewport: viewport });
			});
		});

		var done = 0;
		var failed = [];
		button.disabled = true;

		// One page at a time: light for the server.
		return jobs.reduce(function (chain, job) {
			return chain.then(function () {
				status.textContent = 'Reading the pages: ' + (done + 1) + ' / ' + jobs.length + ' (' + job.viewport.width + 'px) ' + job.url;
				return openFrame(job.url, job.viewport).then(function (opened) {
					var found = collect(opened.win);
					opened.frame.remove();
					Object.keys(found).forEach(function (handle) {
						Object.keys(found[handle]).forEach(function (id) {
							var rule = found[handle][id];
							var current = merged[handle][id];
							if (current && rule.selectors) {
								rule.selectors.forEach(function (s) {
									if (current.selectors.indexOf(s) === -1) current.selectors.push(s);
								});
							} else if (!current) {
								merged[handle][id] = rule;
							}
						});
					});
				}, function (error) {
					failed.push(job.url + ' (' + error.message + ')');
				}).then(function () {
					done++;
				});
			});
		}, Promise.resolve()).then(function () {
			var body = new URLSearchParams();
			body.append('action', 'silver_save_critical_css');
			body.append('nonce', config.nonce);
			config.urls.forEach(function (url) {
				body.append('urls[]', url);
			});
			Object.keys(merged).forEach(function (handle) {
				var css = serialize(merged[handle]);
				if (css) body.append('css[' + handle + ']', css);
			});
			status.textContent = 'Saving...';
			return fetch(config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body });
		}).then(function (response) {
			return response.json();
		}).then(function (json) {
			if (!json || !json.success) throw new Error(json && json.data && json.data.message ? json.data.message : 'Not saved.');
			status.innerHTML = json.data.status + (failed.length ? '<br><span style="color:#996800">Pages not read: ' + failed.join(', ') + '</span>' : '');
		}).catch(function (error) {
			status.textContent = 'Error: ' + error.message;
		}).then(function () {
			button.disabled = false;
		});
	}

	document.addEventListener('click', function (event) {
		var button = event.target.closest ? event.target.closest('#silver-critical-generate') : null;
		if (!button) return;
		event.preventDefault();
		generate(button, document.getElementById('silver-critical-status'));
	});
})();
