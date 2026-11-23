/**
 * "Thêm nhanh" in Product data > Attributes (inc/woocommerce/structure-wc-quick-attributes.php).
 *
 * Asks for the attributes of the ticked product categories, then adds the
 * missing ones one after the other through "Add global" (WooCommerce's own
 * handler adds the row, fixes the positions and disables the attribute in
 * the search), waiting for each row before the next one.
 */
( function ( $, settings ) {
	'use strict';

	if ( ! $ || ! settings ) {
		return;
	}

	const i18n = settings.i18n || {};

	const present = () =>
		$( '#product_attributes .product_attributes .woocommerce_attribute.taxonomy' )
			.filter( function () {
				return $( this ).css( 'display' ) !== 'none';
			} )
			.map( function () {
				return $( this ).data( 'taxonomy' );
			} )
			.get();

	const categories = () =>
		$( '#product_catchecklist input[type="checkbox"]:checked' )
			.map( function () {
				return this.value;
			} )
			.get();

	// One attribute through "Add global"; resolves once WooCommerce added it.
	function addOne( taxonomy ) {
		return new Promise( ( resolve ) => {
			const timer = window.setTimeout( done, 15000 );

			function done() {
				window.clearTimeout( timer );
				$( document.body ).off( 'woocommerce_added_attribute', done );
				resolve();
			}

			$( document.body ).on( 'woocommerce_added_attribute', done );
			$( 'select.wc-attribute-search' ).trigger( {
				type: 'select2:select',
				params: { data: { id: taxonomy } },
			} );
		} );
	}

	$( () => {
		const $actions = $( '#product_attributes .toolbar-top .actions' );
		if ( ! $actions.length ) {
			return;
		}

		const $button = $(
			'<button type="button" class="button silver-quick-attributes"><span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span></button>'
		)
			.append( document.createTextNode( i18n.button || 'Quick add' ) )
			.attr( 'title', i18n.title || '' );
		const $status = $( '<span class="silver-quick-attributes-status" role="status" aria-live="polite"></span>' );

		$actions.append( $button ).after( $status );

		$button.on( 'click', async () => {
			const ids = categories();
			$status.text( '' );

			if ( ! ids.length ) {
				$status.text( i18n.noCategory || '' );
				return;
			}

			$button.prop( 'disabled', true ).attr( 'aria-busy', 'true' );

			try {
				const response = await $.post( settings.ajaxUrl, {
					action: 'silver_quick_attributes',
					security: settings.nonce,
					categories: ids,
				} );

				if ( ! response || ! response.success ) {
					throw new Error( 'request' );
				}

				const wanted = response.data.attributes || [];
				const labels = response.data.labels || {};

				if ( ! wanted.length ) {
					$status.text( i18n.noAttributes || '' );
					return;
				}

				const have = present();
				const missing = wanted.filter( ( taxonomy ) => ! have.includes( taxonomy ) );

				if ( ! missing.length ) {
					$status.text( i18n.allThere || '' );
					return;
				}

				for ( const taxonomy of missing ) {
					await addOne( taxonomy );
				}

				const names = missing.map( ( taxonomy ) => labels[ taxonomy ] || taxonomy ).join( ', ' );
				$status.text( ( i18n.added || '%1$d: %2$s' ).replace( '%1$d', String( missing.length ) ).replace( '%2$s', names ) );
			} catch ( error ) {
				$status.text( i18n.error || '' );
			} finally {
				$button.prop( 'disabled', false ).removeAttr( 'aria-busy' );
			}
		} );
	} );
}( window.jQuery, window.silverQuickAttributes ) );
