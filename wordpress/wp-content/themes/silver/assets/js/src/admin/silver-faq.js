/**
 * FAQs field (inc/woocommerce/structure-wc-faq.php): Add / Edit product
 * category screens, Edit product screen.
 *
 * - The source radios show their panel (Rank Math template, custom questions).
 * - Custom questions: add, remove, drag to reorder, move up / down.
 * - Add term screen: the field is reset after a category is added (AJAX).
 */
( function () {
	'use strict';

	function source( root ) {
		const checked = root.querySelector( 'input[name="silver_faq[source]"]:checked' );
		return checked ? checked.value : '';
	}

	function sync( root ) {
		const value = source( root );
		root.querySelectorAll( '[data-faq-panel]' ).forEach( ( panel ) => {
			panel.hidden = panel.getAttribute( 'data-faq-panel' ) !== value;
		} );
	}

	function addRow( root ) {
		const rows = root.querySelector( '.silver-faq-rows' );
		const template = root.querySelector( 'template.silver-faq-row-template' );
		if ( ! rows || ! template ) {
			return;
		}

		const index = parseInt( rows.getAttribute( 'data-next' ), 10 ) || rows.children.length;
		rows.setAttribute( 'data-next', String( index + 1 ) );
		rows.insertAdjacentHTML( 'beforeend', template.innerHTML.replace( /__i__/g, String( index ) ) );

		const input = rows.lastElementChild && rows.lastElementChild.querySelector( 'input' );
		if ( input ) {
			input.focus();
		}
	}

	function init( root ) {
		if ( root.dataset.faqReady ) {
			return;
		}
		root.dataset.faqReady = '1';

		root.addEventListener( 'change', ( event ) => {
			if ( event.target.matches( 'input[name="silver_faq[source]"]' ) ) {
				sync( root );
			}
		} );

		root.addEventListener( 'click', ( event ) => {
			const button = event.target.closest( 'button' );
			if ( ! button || ! root.contains( button ) ) {
				return;
			}

			const row = button.closest( '.silver-faq-row' );

			if ( button.classList.contains( 'silver-faq-add' ) ) {
				event.preventDefault();
				addRow( root );
			} else if ( row && button.classList.contains( 'silver-faq-remove' ) ) {
				event.preventDefault();
				const next = row.nextElementSibling || row.previousElementSibling;
				row.remove();
				const focus = next ? next.querySelector( 'input' ) : root.querySelector( '.silver-faq-add' );
				if ( focus ) {
					focus.focus();
				}
			} else if ( row && button.classList.contains( 'silver-faq-up' ) && row.previousElementSibling ) {
				event.preventDefault();
				row.parentNode.insertBefore( row, row.previousElementSibling );
				button.focus();
			} else if ( row && button.classList.contains( 'silver-faq-down' ) && row.nextElementSibling ) {
				event.preventDefault();
				row.parentNode.insertBefore( row.nextElementSibling, row );
				button.focus();
			}
		} );

		sortable( root );
		sync( root );
	}

	// Drag the handle to reorder the questions (jQuery UI Sortable, WordPress
	// core). The move up / down buttons stay for the keyboard. The rows are
	// read again at each drag, so added rows can be dragged too.
	function sortable( root ) {
		const rows = root.querySelector( '.silver-faq-rows' );
		if ( ! rows || ! window.jQuery || ! window.jQuery.fn.sortable ) {
			return;
		}

		window.jQuery( rows ).sortable( {
			items: '> .silver-faq-row',
			handle: '.silver-faq-row__handle',
			axis: 'y',
			tolerance: 'pointer',
			placeholder: 'silver-faq-placeholder',
			forcePlaceholderSize: true,
			start( event, ui ) {
				ui.placeholder.height( ui.item.outerHeight() );
			},
		} );
	}

	function reset( root ) {
		const none = root.querySelector( 'input[name="silver_faq[source]"][value=""]' );
		if ( none ) {
			none.checked = true;
		}
		const select = root.querySelector( 'select[name="silver_faq[schema]"]' );
		if ( select ) {
			select.value = '';
		}
		const rows = root.querySelector( '.silver-faq-rows' );
		if ( rows ) {
			rows.innerHTML = '';
			rows.setAttribute( 'data-next', '0' );
		}
		sync( root );
	}

	document.querySelectorAll( '.silver-faq-fields' ).forEach( init );

	// Add term screen: WordPress clears its own fields after a successful add.
	if ( window.jQuery && document.getElementById( 'addtag' ) ) {
		window.jQuery( document ).ajaxSuccess( ( event, xhr, settings ) => {
			const data = settings && typeof settings.data === 'string' ? settings.data : '';
			if ( data.indexOf( 'action=add-tag' ) === -1 || ! xhr.responseXML || xhr.responseXML.querySelector( 'wp_error' ) ) {
				return;
			}
			document.querySelectorAll( '#addtag .silver-faq-fields' ).forEach( reset );
		} );
	}
}() );
