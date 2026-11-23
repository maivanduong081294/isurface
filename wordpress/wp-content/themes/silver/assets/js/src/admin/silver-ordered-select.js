/**
 * Ordered multiple select: selectWoo (select2) that keeps the order chosen,
 * for the attribute fields of product categories and products
 * (structure-wc-card-attributes.php, structure-wc-spec-groups.php).
 *
 * - A chosen option moves to the end of the select.
 * - Dragging the chips reorders the options.
 * So the form posts the values in the order shown. Selects with the class
 * "silver-ordered-select" are set up on load; window.silverOrderedSelect()
 * sets up one added later.
 */
( function ( $ ) {
	'use strict';

	if ( ! $ ) {
		return;
	}

	function orderOptions( $select ) {
		$select.next( '.select2' ).find( '.select2-selection__choice' ).each( function () {
			const data = $( this ).data( 'data' );
			if ( data && data.element ) {
				$( data.element ).detach().appendTo( $select );
			}
		} );
	}

	function setup( select ) {
		const $select = $( select );
		if ( ! select || ! $.fn.selectWoo || $select.data( 'select2' ) ) {
			return;
		}

		$select.selectWoo( {
			width: '100%',
			closeOnSelect: false,
			placeholder: $select.data( 'placeholder' ) || '',
		} );

		// The option chosen last goes last.
		$select.on( 'select2:select', ( event ) => {
			const option = event.params && event.params.data ? event.params.data.element : null;
			if ( option ) {
				$( option ).detach().appendTo( $select );
				$select.trigger( 'change' );
			}
		} );

		// Drag the chips to reorder.
		const $list = $select.next( '.select2' ).find( 'ul.select2-selection__rendered' );
		if ( $.fn.sortable && $list.length ) {
			$list.sortable( {
				items: '> .select2-selection__choice',
				containment: 'parent',
				tolerance: 'pointer',
				stop: () => {
					orderOptions( $select );
					$select.trigger( 'change' );
				},
			} );
		}
	}

	window.silverOrderedSelect = setup;

	$( () => {
		document.querySelectorAll( 'select.silver-ordered-select' ).forEach( ( select ) => {
			// Not the rows of a <template> (set up once added).
			if ( ! select.closest( 'template' ) ) {
				setup( select );
			}
		} );
	} );
}( window.jQuery ) );
