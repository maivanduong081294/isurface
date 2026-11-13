/**
 * Silver Dialog — popup alert / confirm / notice dùng chung cho mọi file JS.
 *
 * Thay cho alert() / confirm() mặc định của trình duyệt. Popup HTML không thể
 * chặn luồng code như confirm() gốc, nên mọi hàm đều trả về Promise:
 *
 *   await silverDialog.alert('Đã lưu thành công', { type: 'success' });
 *
 *   if (await silverDialog.confirm('Xoá sản phẩm này?', { icon: 'warning' })) {
 *     // người dùng bấm "Đồng ý"
 *   }
 *
 *   // Xử lý bất đồng bộ trong nút "Đồng ý": popup hiện loading và chỉ đóng
 *   // khi Promise xong. Nếu Promise lỗi, popup mở lại các nút để thử lại.
 *   silverDialog.confirm('Gửi đơn hàng?', {
 *     onYes: () => fetch('/api/order', { method: 'POST' }),
 *   });
 *
 * Nội dung mặc định được in dạng text (chống XSS). Truyền { html: true } khi
 * nội dung là HTML do chính code của bạn tạo ra, không phải dữ liệu người dùng.
 *
 * Để dùng trong file JS khác, khai báo phụ thuộc 'silver-dialog' khi enqueue:
 *   wp_enqueue_script( 'my-script', $src, array( 'silver-dialog' ), $ver, true );
 *
 * Ngoài ra có các hàm kiểu callback (nhận nội dung HTML):
 *   silver_add_notice(message, type, onClose)
 *   silver_popper_notice(message, type, onClose)
 *   silver_add_popper(content, extraClass, onClose)
 *   silver_add_confirm(content, onYes(close, setLoading), onNo, options)
 *   silver_popper_confirm(content, onYes, onNo, options)
 */
(function (window, document) {
	'use strict';

	if (window.silverDialog) return;

	const config = Object.assign(
		{
			okText: 'Đóng',
			yesText: 'Đồng ý',
			noText: 'Huỷ',
			closeLabel: 'Đóng',
			overrideAlert: false,
		},
		window.silverDialogConfig || {}
	);

	const ICONS = {
		// pathLength="1": CSS vẽ từng nét bằng cùng một animation (stroke-dashoffset 1 -> 0).
		success: '<path pathLength="1" d="M20 6 9 17l-5-5"/>',
		warning: '<path pathLength="1" d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path pathLength="1" d="M12 9v4m0 4h.01"/>',
		error: '<path pathLength="1" d="M18 6 6 18"/><path pathLength="1" d="M6 6l12 12"/>',
		info: '<circle pathLength="1" cx="12" cy="12" r="9"/><path pathLength="1" d="M12 16v-4m0-4h.01"/>',
	};

	// Trang chưa nạp silver-dialog.css (ví dụ trong admin): tự chèn vào.
	function ensureStyle() {
		if (!config.styleUrl || document.getElementById('silver-dialog-css')) return;
		const link = document.createElement('link');
		link.id = 'silver-dialog-css';
		link.rel = 'stylesheet';
		link.href = config.styleUrl;
		document.head.appendChild(link);
	}

	const HEADLINE_MAX = 80; // Số ký tự tối đa để nội dung được coi là "dòng chính".
	const nativeAlert = window.alert ? window.alert.bind(window) : null;
	const open = []; // Dialog đang mở, cái trên cùng ở cuối.
	let uid = 0;

	function normalizeType(type, fallback) {
		return Object.prototype.hasOwnProperty.call(ICONS, type) ? type : fallback;
	}

	function iconMarkup(type) {
		if (!type || !ICONS[type]) return '';
		return (
			'<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' +
			ICONS[type] +
			'</svg>'
		);
	}

	// Gán nội dung: text (mặc định, an toàn) hoặc HTML / Node khi được yêu cầu.
	function setContent(element, content, asHtml) {
		if (content instanceof Node) {
			element.appendChild(content);
		} else if (asHtml) {
			element.innerHTML = content == null ? '' : String(content);
		} else {
			element.textContent = content == null ? '' : String(content);
		}
	}

	function createButton(className, text) {
		const button = document.createElement('button');
		button.type = 'button';
		button.className = 'silver-dialog__btn ' + className;
		const label = document.createElement('span');
		label.className = 'silver-dialog__btn-text';
		label.textContent = text;
		button.appendChild(label);
		return button;
	}

	/**
	 * Mở một dialog. Hàm nội bộ dùng chung cho alert / confirm / popup.
	 *
	 * @param {Object} opts
	 * @returns {Object} { element, close(value), setLoading(bool), promise }
	 */
	function openDialog(opts) {
		ensureStyle();
		const id = 'silver-dialog-' + ++uid;
		const previousFocus = document.activeElement;
		let closed = false;
		let loading = false;
		let timer = null;
		let resolvePromise;
		const promise = new Promise((resolve) => (resolvePromise = resolve));

		const root = document.createElement('div');
		root.className = ['silver-dialog', 'silver-dialog--' + opts.kind, opts.type ? 'silver-dialog--' + opts.type : '', opts.className || '']
			.filter(Boolean)
			.join(' ');
		root.id = opts.id || id;

		const overlay = document.createElement('div');
		overlay.className = 'silver-dialog__overlay';

		const box = document.createElement('div');
		box.className = 'silver-dialog__box';
		box.setAttribute('role', opts.kind === 'confirm' ? 'alertdialog' : 'dialog');
		box.setAttribute('aria-modal', 'true');
		box.tabIndex = -1;

		if (opts.showClose) {
			const closeBtn = document.createElement('button');
			closeBtn.type = 'button';
			closeBtn.className = 'silver-dialog__close';
			closeBtn.setAttribute('aria-label', opts.closeLabel || config.closeLabel);
			closeBtn.addEventListener('click', () => dismiss());
			box.appendChild(closeBtn);
		}

		const icon = iconMarkup(opts.type);
		if (icon) {
			const iconWrap = document.createElement('div');
			iconWrap.className = 'silver-dialog__icon';
			iconWrap.innerHTML = icon; // SVG cố định, không chứa dữ liệu ngoài.
			box.appendChild(iconWrap);
		}

		if (opts.title) {
			const title = document.createElement('div');
			title.className = 'silver-dialog__title';
			title.id = root.id + '-title';
			setContent(title, opts.title, opts.html);
			box.appendChild(title);
			box.setAttribute('aria-labelledby', title.id);
		}

		const body = document.createElement('div');
		body.className = 'silver-dialog__message';
		body.id = root.id + '-message';
		setContent(body, opts.message, opts.html);
		box.appendChild(body);

		// Không có tiêu đề và nội dung ngắn, một dòng: nội dung là dòng chính
		// (to, đậm). Nội dung dài hoặc có khối HTML thì giữ kiểu chữ thường.
		if (
			opts.kind !== 'popup' &&
			!opts.title &&
			body.textContent.trim().length <= HEADLINE_MAX &&
			!body.querySelector('p, div, ul, ol, table, form, h1, h2, h3, h4, h5, h6, br')
		) {
			root.classList.add('silver-dialog--headline');
		}
		box.setAttribute('aria-describedby', body.id);

		const buttons = [];
		if (opts.buttons && opts.buttons.length) {
			const actions = document.createElement('div');
			actions.className = 'silver-dialog__actions';
			opts.buttons.forEach((def) => {
				const button = createButton(def.className, def.text);
				button.addEventListener('click', () => {
					if (!loading && !closed) def.onClick(api);
				});
				actions.appendChild(button);
				buttons.push(button);
				def.element = button;
			});
			box.appendChild(actions);
		}

		// Tự đóng (timeout): thanh chạy dưới đáy khung cho biết còn bao lâu.
		if (opts.timeout > 0) {
			const progress = document.createElement('div');
			progress.className = 'silver-dialog__progress';
			progress.setAttribute('aria-hidden', 'true');
			root.style.setProperty('--silver-dialog-timeout', opts.timeout + 'ms');
			box.appendChild(progress);
		}

		root.appendChild(overlay);
		root.appendChild(box);

		function close(value) {
			if (closed) return;
			closed = true;
			clearTimeout(timer);
			document.removeEventListener('keydown', onKeydown, true);

			const index = open.indexOf(api);
			if (index > -1) open.splice(index, 1);
			if (!open.length) document.documentElement.classList.remove('silver-dialog-open');

			root.classList.remove('is-visible');
			root.classList.add('is-closing');
			buttons.forEach((button) => (button.disabled = true));
			const remove = () => root.parentNode && root.parentNode.removeChild(root);
			const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			reduced ? remove() : setTimeout(remove, 200);

			if (previousFocus && typeof previousFocus.focus === 'function' && document.contains(previousFocus)) {
				previousFocus.focus();
			}

			resolvePromise(value);
			if (typeof opts.onClose === 'function') opts.onClose(value);
		}

		// Đóng bằng Esc / overlay / nút X: giá trị "huỷ" của từng loại dialog.
		function dismiss() {
			if (loading || closed) return;
			close(opts.dismissValue);
		}

		function setLoading(isLoading) {
			loading = !!isLoading;
			root.classList.toggle('is-loading', loading);
			buttons.forEach((button) => (button.disabled = loading));
			if (opts.loadingButton != null && buttons[opts.loadingButton]) {
				buttons[opts.loadingButton].classList.toggle('is-loading', loading);
				buttons[opts.loadingButton].setAttribute('aria-busy', loading ? 'true' : 'false');
			}
		}

		function onKeydown(event) {
			if (open[open.length - 1] !== api) return; // Chỉ dialog trên cùng nhận phím.

			if (event.key === 'Escape') {
				event.preventDefault();
				if (opts.closeOnEsc !== false) dismiss();
				return;
			}

			// Giữ focus bên trong dialog.
			if (event.key === 'Tab') {
				const focusable = Array.from(box.querySelectorAll('button:not([disabled]), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'));
				if (!focusable.length) {
					event.preventDefault();
					box.focus();
					return;
				}
				const first = focusable[0];
				const last = focusable[focusable.length - 1];
				if (event.shiftKey && document.activeElement === first) {
					event.preventDefault();
					last.focus();
				} else if (!event.shiftKey && document.activeElement === last) {
					event.preventDefault();
					first.focus();
				}
			}
		}

		// Bấm ra ngoài: chỉ đóng khi được cho phép (closeOnOverlay). Mặc định
		// alert / notice / confirm phải đóng bằng nút, nên khung chỉ rung nhẹ
		// để nhắc người dùng chọn một nút.
		overlay.addEventListener('click', () => {
			if (opts.closeOnOverlay) {
				dismiss();
				return;
			}
			if (loading || closed) return;
			root.classList.remove('is-nudge');
			void root.offsetWidth; // Chạy lại animation.
			root.classList.add('is-nudge');
		});
		box.addEventListener('animationend', (event) => {
			if (event.target === box) root.classList.remove('is-nudge');
		});
		document.addEventListener('keydown', onKeydown, true);

		const api = { element: root, close, setLoading, promise, dismissValue: opts.dismissValue };
		open.push(api);

		root.style.zIndex = String(100000 + open.length);
		document.body.appendChild(root);
		document.documentElement.classList.add('silver-dialog-open');

		// Frame sau mới thêm class để transition chạy.
		requestAnimationFrame(() => requestAnimationFrame(() => root.classList.add('is-visible')));

		const focusTarget = buttons[opts.focusButton != null ? opts.focusButton : buttons.length - 1] || box;
		setTimeout(() => focusTarget.focus(), 30);

		if (opts.timeout > 0) timer = setTimeout(() => close(opts.dismissValue), opts.timeout);

		return api;
	}

	/**
	 * Thông báo, thay cho alert().
	 *
	 * @param {string|Node} message
	 * @param {Object|string} [options] Hoặc truyền thẳng type.
	 *   type: 'success' | 'warning' | 'error' | 'info' | '' (mặc định không icon)
	 *   title, okText, html (bool), timeout (ms, tự đóng), className, onClose
	 *   closeOnOverlay (bool, mặc định false): bấm ra ngoài có đóng không
	 *   closeOnEsc (bool, mặc định true): phím Esc có đóng không
	 * @returns {Promise<void>} Xong khi dialog đóng.
	 */
	function alert(message, options) {
		const opts = typeof options === 'string' ? { type: options } : Object.assign({}, options);

		return openDialog({
			kind: 'alert',
			type: normalizeType(opts.type, ''),
			title: opts.title,
			message,
			html: !!opts.html,
			className: opts.className,
			id: opts.id,
			timeout: opts.timeout,
			showClose: false, // Đã có nút Đóng.
			closeOnOverlay: opts.closeOnOverlay === true,
			closeOnEsc: opts.closeOnEsc,
			dismissValue: undefined,
			onClose: opts.onClose,
			buttons: [
				{
					className: 'silver-dialog__btn--primary silver-dialog__ok',
					text: opts.okText || config.okText,
					onClick: (dialog) => dialog.close(undefined),
				},
			],
		}).promise;
	}

	/**
	 * Hộp xác nhận, thay cho confirm().
	 *
	 * @param {string|Node} message
	 * @param {Object} [options]
	 *   title, icon ('success' | 'warning' | 'error' | 'info'), yesText, noText,
	 *   html (bool), className, danger (bool: nút Đồng ý màu đỏ)
	 *   closeOnOverlay (bool, mặc định false): bấm ra ngoài có tính là Huỷ không
	 *   closeOnEsc (bool, mặc định true): phím Esc có tính là Huỷ không
	 *   onYes(close, setLoading): nếu có, popup KHÔNG tự đóng khi bấm Đồng ý.
	 *     - Trả về Promise: popup hiện loading, đóng khi Promise xong, mở lại
	 *       các nút nếu Promise lỗi.
	 *     - Không trả về Promise: tự gọi close() khi xử lý xong (giống silver_add_confirm).
	 *   onNo(): gọi khi bấm Huỷ (hoặc Esc).
	 * @returns {Promise<boolean>} true khi đã đồng ý, false khi huỷ.
	 */
	function confirm(message, options) {
		const opts = Object.assign({}, options);
		let dialog;

		const handleNo = () => {
			dialog.close(false);
		};

		const handleYes = () => {
			if (typeof opts.onYes !== 'function') {
				dialog.close(true);
				return;
			}

			const done = () => dialog.close(true);
			dialog.setLoading(true);

			let result;
			try {
				result = opts.onYes(done, dialog.setLoading);
			} catch (error) {
				dialog.setLoading(false);
				console.error(error);
				return;
			}

			if (result && typeof result.then === 'function') {
				result.then(done, (error) => {
					dialog.setLoading(false);
					if (error) console.error(error);
				});
			}
		};

		dialog = openDialog({
			kind: 'confirm',
			type: normalizeType(opts.icon, ''),
			title: opts.title,
			message,
			html: !!opts.html,
			className: [opts.className, opts.danger ? 'silver-dialog--danger' : ''].filter(Boolean).join(' '),
			id: opts.id,
			showClose: false,
			closeOnOverlay: opts.closeOnOverlay === true,
			closeOnEsc: opts.closeOnEsc,
			dismissValue: false,
			loadingButton: 1,
			focusButton: 1,
			onClose: (value) => {
				if (value === false && typeof opts.onNo === 'function') opts.onNo();
			},
			buttons: [
				{ className: 'silver-dialog__btn--secondary silver-dialog__no', text: opts.noText || config.noText, onClick: handleNo },
				{ className: 'silver-dialog__btn--primary silver-dialog__yes', text: opts.yesText || config.yesText, onClick: handleYes },
			],
		});

		return dialog.promise;
	}

	/**
	 * Popup chứa nội dung tuỳ ý (form, HTML...). Không có nút hành động.
	 *
	 * @param {string|Node} content
	 * @param {Object} [options] title, html (mặc định true), className, onClose,
	 *   closeOnOverlay (mặc định true), closeOnEsc (mặc định true)
	 * @returns {Object} { element, close(), promise }
	 */
	function popup(content, options) {
		const opts = Object.assign({ html: true }, options);
		const dialog = openDialog({
			kind: 'popup',
			type: '',
			title: opts.title,
			message: content,
			html: !!opts.html,
			className: opts.className,
			id: opts.id,
			showClose: true,
			closeOnOverlay: opts.closeOnOverlay !== false,
			closeOnEsc: opts.closeOnEsc,
			dismissValue: undefined,
			onClose: opts.onClose,
			buttons: [],
		});

		return { element: dialog.element, close: () => dialog.close(undefined), promise: dialog.promise };
	}

	/** Đóng tất cả dialog đang mở (confirm nhận false, như bấm Huỷ). */
	function closeAll() {
		open.slice().forEach((dialog) => dialog.close(dialog.dismissValue));
	}

	const silverDialog = { alert, confirm, popup, closeAll, nativeAlert };

	/**
	 * Ghi đè window.alert() để mọi alert (kể cả của plugin) hiện bằng popup.
	 * Lưu ý: code gọi alert() sẽ KHÔNG còn bị dừng lại chờ người dùng bấm.
	 * Không thể làm điều tương tự với confirm(), vì confirm() phải trả về
	 * true/false ngay lập tức.
	 */
	silverDialog.overrideAlert = function () {
		window.alert = (message) => {
			alert(message);
		};
	};
	silverDialog.restoreAlert = function () {
		if (nativeAlert) window.alert = nativeAlert;
	};

	window.silverDialog = silverDialog;

	if (config.overrideAlert) silverDialog.overrideAlert();

	/* ------------------------------------------------------------------ */
	/* Hàm kiểu callback silver_* (nội dung nhận HTML).                     */
	/* ------------------------------------------------------------------ */

	window.silver_popper_notice = function (message, type, onClose) {
		return alert(message, { type: normalizeType(type || 'success', 'error'), html: true, onClose });
	};

	window.silver_add_notice = function (message, type, onClose) {
		return window.silver_popper_notice(message, type || 'error', onClose);
	};

	window.silver_add_popper = function (content, extraClass, onClose) {
		return popup(content, { className: extraClass, onClose });
	};

	window.silver_popper_confirm = function (content, onYes, onNo, options) {
		return confirm(content, Object.assign({ html: true }, options, { onYes: onYes || undefined, onNo: onNo || undefined }));
	};

	window.silver_add_confirm = window.silver_popper_confirm;
})(window, document);
