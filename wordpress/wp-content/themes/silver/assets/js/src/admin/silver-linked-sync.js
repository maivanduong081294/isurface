/**
 * Buttons next to Upsells and Cross-sells (inc/woocommerce/structure-wc-linked-sync.php).
 *
 * - "Gửi sang SP khác": the list shown in the field, saved or not, is written
 *   into the products picked in the dialog right away (AJAX), with the heading.
 * - "Lấy từ SP khác": the list (and the heading) of the product picked in the
 *   dialog fills the field; it is stored with "Update", like a manual edit.
 *
 * The heading fields are printed after the panel and moved under their list.
 *
 * The dialog lists the products of the ticked categories, or those found by the search.
 */
( function ( $, settings ) {
	'use strict';

	if ( ! $ || ! settings ) {
		return;
	}

	const i18n = settings.i18n || {};
	const FIELDS = { upsell: '#upsell_ids', crosssell: '#crosssell_ids' };
	const HEADINGS = { upsell: '#_silver_upsell_heading', crosssell: '#_silver_crosssell_heading' };

	const format = ( template, ...values ) => {
		let i = 0;
		return String( template || '' ).replace( /%(\d\$)?[sd]/g, ( match, position ) => String( values[ position ? parseInt( position, 10 ) - 1 : i++ ] ) );
	};

	const post = ( action, data ) =>
		$.post( settings.ajaxUrl, Object.assign( { action, security: settings.nonce, product_id: settings.productId || $( '#post_ID' ).val() }, data ) ).then( ( response ) =>
			response && response.success ? response.data : $.Deferred().reject( response ).promise()
		);

	const errorOf = ( response ) => ( response && response.data && response.data.message ) || ( response && response.responseJSON && response.responseJSON.data && response.responseJSON.data.message ) || '';

	const categories = () =>
		$( '#product_catchecklist input[type="checkbox"]:checked' )
			.map( function () {
				return this.value;
			} )
			.get();

	/**
	 * Puts the items ({id, text}) into the select2 field.
	 *
	 * @return {boolean} Whether the field changed.
	 */
	function fill( $select, items, replace ) {
		const before = ( $select.val() || [] ).map( String );

		if ( replace ) {
			$select.find( 'option' ).prop( 'selected', false );
		}

		items.forEach( ( item ) => {
			const id = String( item.id );
			const $option = $select.find( 'option' ).filter( function () {
				return this.value === id;
			} );

			$option.length ? $option.prop( 'selected', true ) : $select.append( new Option( item.text, id, true, true ) );
		} );

		const changed = ( $select.val() || [] ).map( String ).join() !== before.join();
		if ( changed ) {
			$select.trigger( 'change' );
		}

		return changed;
	}

	/**
	 * Dialog of a field.
	 *
	 * @param {string}  field upsell | crosssell.
	 * @param {boolean} pull  true: "Lấy từ SP khác" (one source), false: "Gửi sang SP khác" (many targets).
	 */
	function open( field, pull ) {
		const dialog = window.silverDialog;
		if ( ! dialog ) {
			return;
		}

		const text = pull ? Object.assign( {}, i18n, i18n.pull || {} ) : i18n;
		const $select = $( FIELDS[ field ] );
		const $heading = $( HEADINGS[ field ] );
		const heading = String( $heading.val() || '' ).trim();
		const ids = ( $select.val() || [] ).map( String );
		const picked = new Map(); // id => product, kept across searches.
		const fieldName = ( i18n.fields || {} )[ field ] || field;
		const boxName = 'silver-lsync-pick-' + Date.now();

		const $form = $( '<div class="silver-lsync"></div>' );
		const $search = $( '<input type="search" class="silver-lsync__search">' ).attr( 'placeholder', i18n.search || '' );
		const $count = $( '<span></span>' );
		const $all = $( '<input type="checkbox">' );
		const $list = $( '<div class="silver-lsync__list" role="group"></div>' );
		const $hint = $( '<p class="silver-lsync__muted"></p>' );
		const $error = $( '<p class="silver-lsync__error" role="alert"></p>' );
		const radio = ( value, checked, label ) =>
			$( '<label></label>' ).append( $( '<input type="radio" name="silver-lsync-mode">' ).val( value ).prop( 'checked', checked ), document.createTextNode( label || '' ) );
		const $extra = $( '<input type="checkbox">' );
		// The heading always goes with the list (both ways), as it is.
		const headingNote = pull ? text.heading : heading ? format( text.heading, heading ) : text.headingEmpty;

		$form.append(
			$( '<p></p>' ).text( ids.length ? format( i18n.list, ids.length ) : i18n.emptyList ),
			$search,
			$( '<div class="silver-lsync__bar"></div>' ).append(
				$( '<span></span>' ).append( document.createTextNode( ( text.products || '' ) + ' · ' ), $count ),
				pull ? null : $( '<label></label>' ).append( $all, document.createTextNode( i18n.all || '' ) )
			),
			$list,
			$hint,
			$( '<div class="silver-lsync__options"></div>' ).append(
				radio( 'replace', true, text.replace ),
				radio( 'merge', false, text.merge ),
				$( '<label></label>' ).append( $extra, document.createTextNode( ( pull ? text.withSource : text.withSelf ) || '' ) )
			),
			$heading.length ? $( '<p class="silver-lsync__muted"></p>' ).text( headingNote || '' ) : null,
			$error
		);

		const updateCount = () => {
			if ( pull ) {
				const product = picked.values().next().value;
				$count.text( product ? product.name : text.picked );
				$list.find( '.silver-lsync__item' ).each( function () {
					$( this ).toggleClass( 'is-picked', $( this ).find( 'input' ).prop( 'checked' ) );
				} );
				return;
			}

			$count.text( format( text.picked, picked.size ) );
			const $boxes = $list.find( 'input:enabled' );
			$all.prop( 'checked', $boxes.length > 0 && $boxes.filter( ':checked' ).length === $boxes.length );
		};

		function render( products, more, searching ) {
			$list.empty();
			if ( ! products.length ) {
				$list.append( $( '<div class="silver-lsync__empty"></div>' ).text( i18n.none || '' ) );
			}

			products.forEach( ( product ) => {
				// No cross-sells on grouped / external products: nothing to write (or to take).
				const disabled = field === 'crosssell' && product.noCross;
				const id = String( product.id );
				const meta = [ product.sku, product.status, disabled ? i18n.noCross : format( i18n.has, product[ field ] ) ].filter( Boolean ).join( ' · ' );
				const $box = $( pull ? '<input type="radio">' : '<input type="checkbox">' )
					.attr( 'name', boxName )
					.val( id )
					.prop( { checked: picked.has( id ), disabled } );

				$box.on( 'change', function () {
					if ( pull ) {
						picked.clear();
					}
					this.checked ? picked.set( id, product ) : picked.delete( id );
					updateCount();
				} );

				$list.append(
					$( '<label class="silver-lsync__item"></label>' )
						.toggleClass( 'is-disabled', disabled )
						.append(
							$box,
							$( '<img alt="" loading="lazy">' ).attr( 'src', product.image ),
							$( '<span class="silver-lsync__name"></span>' ).text( product.name + ' (#' + id + ')' ).append( $( '<small></small>' ).text( meta ) )
						)
				);
			} );

			$hint.text( more ? i18n.more : searching ? '' : categories().length ? i18n.sameCat : i18n.typeToSearch );
			updateCount();
		}

		let request = 0;
		function load() {
			const search = $search.val().trim();
			const current = ++request;

			$list.empty().append( $( '<div class="silver-lsync__empty"></div>' ).text( i18n.loading || '' ) );
			post( 'silver_linked_sync_products', { search, categories: categories() } )
				.then( ( data ) => current === request && render( data.products || [], data.more, search !== '' ) )
				.catch( () => current === request && render( [], false, true ) );
		}

		let timer = 0;
		$search.on( 'input', () => {
			window.clearTimeout( timer );
			timer = window.setTimeout( load, 350 );
		} );
		// Enter searches at once instead of pressing the dialog's button.
		$search.on( 'keydown', ( event ) => {
			if ( event.key === 'Enter' ) {
				event.preventDefault();
				window.clearTimeout( timer );
				load();
			}
		} );

		$all.on( 'change', () => {
			$list.find( 'input:enabled' ).prop( 'checked', $all.prop( 'checked' ) ).trigger( 'change' );
		} );

		const fail = ( message, setLoading ) => {
			$error.text( message || '' );
			setLoading( false );
		};

		// "Lấy từ SP khác": fills the field, nothing is saved.
		function take( close, setLoading ) {
			const mode = $form.find( 'input[name="silver-lsync-mode"]:checked' ).val();
			const source = picked.keys().next().value;

			if ( ! source ) {
				fail( text.pick, setLoading );
				return;
			}

			post( 'silver_linked_sync_source', { field, source, with_source: $extra.prop( 'checked' ) ? 1 : 0 } )
				.then( ( data ) => {
					const items = data.items || [];
					if ( ! items.length ) {
						fail( format( text.empty, data.name ), setLoading );
						return;
					}

					let changed = fill( $select, items, mode === 'replace' );
					const sourceHeading = String( data.heading || '' );
					if ( $heading.length && sourceHeading !== $heading.val() ) {
						$heading.val( sourceHeading ).trigger( 'change' );
						changed = true;
					}
					close();
					dialog.alert( changed ? format( text.done, items.length, data.name ) : format( text.same, data.name ), {
						type: changed ? 'success' : 'info',
						timeout: 5000,
					} );
				} )
				.catch( ( response ) => fail( errorOf( response ) || text.error, setLoading ) );
		}

		// "Gửi sang SP khác": writes into the picked products (list and heading).
		function push( close, setLoading ) {
			const mode = $form.find( 'input[name="silver-lsync-mode"]:checked' ).val();
			const withSelf = $extra.prop( 'checked' );

			if ( ! picked.size ) {
				fail( text.pick, setLoading );
				return;
			}

			const payload = { field, mode, with_self: withSelf ? 1 : 0, ids, targets: Array.from( picked.keys() ) };
			// Sent even when empty: their heading becomes the default one too.
			if ( $heading.length ) {
				payload.heading = heading;
			}

			post( 'silver_linked_sync_apply', payload )
				.then( ( data ) => {
					close();
					const message = [ data.updated ? format( text.done, data.updated ) : text.same, data.skipped ? format( text.skipped, data.skipped ) : '' ]
						.filter( Boolean )
						.join( ' ' );
					dialog.alert( message, { type: data.updated ? 'success' : 'info', timeout: data.skipped ? 0 : 4000 } );
				} )
				.catch( ( response ) => fail( errorOf( response ) || text.error, setLoading ) );
		}

		dialog.confirm( $form[ 0 ], {
			title: format( text.title, fieldName ),
			yesText: text.apply,
			noText: i18n.cancel,
			className: 'silver-lsync-dialog',
			onYes( close, setLoading ) {
				$error.text( '' );
				( pull ? take : push )( close, setLoading );
			},
		} );

		load();
		window.setTimeout( () => $search.trigger( 'focus' ), 60 );
	}

	$( () => {
		const pullText = i18n.pull || {};

		Object.keys( FIELDS ).forEach( ( field ) => {
			const $select = $( FIELDS[ field ] );
			if ( ! $select.length ) {
				return;
			}

			const button = ( label, title, icon, pull ) =>
				$( '<button type="button" class="button silver-lsync-btn"></button>' )
					.addClass( 'silver-lsync-btn--' + ( pull ? 'pull' : 'push' ) )
					.attr( 'title', title || '' )
					.append( $( '<span class="dashicons" aria-hidden="true"></span>' ).addClass( icon ), document.createTextNode( label ) )
					.on( 'click', () => open( field, pull ) );

			const $buttons = $( '<span class="silver-lsync-actions"></span>' ).append(
				button( i18n.button || 'Send', i18n.buttonTitle, 'dashicons-upload', false ),
				button( pullText.button || 'Copy', pullText.buttonTitle, 'dashicons-download', true )
			);
			const $tip = $select.siblings( '.woocommerce-help-tip' ).first();

			$buttons.insertAfter( $tip.length ? $tip : $select.next( '.select2' ).length ? $select.next( '.select2' ) : $select );

			$( HEADINGS[ field ] ).closest( '.form-field' ).insertAfter( $select.closest( '.form-field' ) );
		} );
	} );
} )( window.jQuery, window.silverLinkedSync );
