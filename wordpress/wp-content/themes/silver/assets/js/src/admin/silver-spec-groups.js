/**
 * Specification groups of product categories (inc/woocommerce/structure-wc-spec-groups.php).
 *
 * - Rows: add (from the template), remove, drag to reorder.
 * - Attributes: select2 multiple that keeps the order chosen
 *   (silver-ordered-select.js).
 * - Add term screen: the field is reset after a category is added (AJAX).
 */
( function ( $ ) {
	'use strict';

	if ( ! $ ) {
		return;
	}

	// Ordered select2 (silver-ordered-select.js): keeps the order chosen.
	const initSelect = ( select ) => {
		if ( window.silverOrderedSelect ) {
			window.silverOrderedSelect( select );
		}
	};

	function init( root ) {
		if ( root.dataset.specReady ) {
			return;
		}
		root.dataset.specReady = '1';

		const rows = root.querySelector( '.silver-spec-groups__rows' );
		const template = root.querySelector( 'template.silver-spec-groups__template' );

		rows.querySelectorAll( '.silver-spec-row__attributes' ).forEach( initSelect );

		root.addEventListener( 'click', ( event ) => {
			const button = event.target.closest( 'button' );
			if ( ! button || ! root.contains( button ) ) {
				return;
			}

			if ( button.classList.contains( 'silver-spec-groups__add' ) ) {
				event.preventDefault();
				const index = parseInt( rows.getAttribute( 'data-next' ), 10 ) || rows.children.length;
				rows.setAttribute( 'data-next', String( index + 1 ) );
				rows.insertAdjacentHTML( 'beforeend', template.innerHTML.replace( /__i__/g, String( index ) ) );

				const row = rows.lastElementChild;
				initSelect( row.querySelector( '.silver-spec-row__attributes' ) );
				row.querySelector( 'input' ).focus();
			} else if ( button.classList.contains( 'silver-spec-row__remove' ) ) {
				event.preventDefault();
				const row = button.closest( '.silver-spec-row' );
				const next = row.nextElementSibling || row.previousElementSibling;
				row.remove();
				const focus = next ? next.querySelector( 'input' ) : root.querySelector( '.silver-spec-groups__add' );
				if ( focus ) {
					focus.focus();
				}
			}
		} );

		if ( $.fn.sortable ) {
			$( rows ).sortable( {
				items: '> .silver-spec-row',
				handle: '.silver-spec-row__handle',
				axis: 'y',
				tolerance: 'pointer',
				placeholder: 'silver-spec-placeholder',
				forcePlaceholderSize: true,
				start( event, ui ) {
					ui.placeholder.height( ui.item.outerHeight() );
				},
			} );
		}
	}

	$( () => {
		document.querySelectorAll( '.silver-spec-groups' ).forEach( init );

		// Add term screen: WordPress clears its own fields after a successful add.
		if ( document.getElementById( 'addtag' ) ) {
			$( document ).ajaxSuccess( ( event, xhr, settings ) => {
				const data = settings && typeof settings.data === 'string' ? settings.data : '';
				if ( data.indexOf( 'action=add-tag' ) === -1 || ! xhr.responseXML || xhr.responseXML.querySelector( 'wp_error' ) ) {
					return;
				}
				document.querySelectorAll( '#addtag .silver-spec-groups__rows' ).forEach( ( rows ) => {
					rows.innerHTML = '';
					rows.setAttribute( 'data-next', '0' );
				} );
			} );
		}
	} );
}( window.jQuery ) );
