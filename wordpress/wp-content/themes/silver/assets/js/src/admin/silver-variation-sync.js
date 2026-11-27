/**
 * "Đồng bộ" on each variation (inc/woocommerce/structure-wc-variation-sync.php).
 *
 * Copies the chosen groups of fields of one variation into the other
 * variations loaded on the page. Only the form changes: every copied field
 * fires "change", so WooCommerce marks the variation as edited and enables
 * "Save changes", exactly like a manual edit. Nothing is stored before that.
 *
 * Never copied: the header (attributes, ID, order), SKU, GTIN, the stock
 * value WooCommerce compares on save, and downloadable files (their inputs
 * are keyed by variation ID).
 */
( function ( $, settings ) {
	'use strict';

	if ( ! $ || ! settings ) {
		return;
	}

	const i18n = settings.i18n || {};
	const groupLabels = i18n.groups || {};
	const ROOT = '#variable_product_options';
	const STORAGE_KEY = 'silverVariationSyncGroups';

	// Field name without its "[loop]" => group. Unknown names go to "other".
	const GROUPS = [
		{ id: 'image', on: true, fields: [ 'upload_image_id', 'variable_gallery_image_ids', 'ux_additional_variation_images_thumbs' ] },
		{ id: 'regular_price', on: false, fields: [ 'variable_regular_price' ] },
		{ id: 'sale_price', on: false, fields: [ 'variable_sale_price', 'variable_sale_price_dates_from', 'variable_sale_price_dates_to' ] },
		{ id: 'options', on: true, fields: [ 'variable_enabled', 'variable_is_downloadable', 'variable_is_virtual' ] },
		{ id: 'stock', on: false, fields: [ 'variable_manage_stock', 'variable_stock', 'variable_backorders', 'variable_low_stock_amount', 'variable_stock_status' ] },
		{ id: 'shipping', on: true, fields: [ 'variable_weight', 'variable_length', 'variable_width', 'variable_height', 'variable_shipping_class' ] },
		{ id: 'tax', on: true, fields: [ 'variable_tax_class' ] },
		{ id: 'description', on: true, fields: [ 'variable_description' ] },
		{ id: 'other', on: false, fields: [] },
	];

	const SKIP = [
		'variable_post_id',
		'variation_menu_order',
		'variable_sku',
		'variable_global_unique_id',
		'variable_original_stock',
		'variable_download_limit',
		'variable_download_expiry',
	];

	// name="variable_regular_price[3]" (or "...[3][]" for multiple values).
	const NAME = /^([^[\]]+)\[(\d+)\](.*)$/;

	const groupOf = ( base ) => ( GROUPS.find( ( group ) => group.fields.includes( base ) ) || { id: 'other' } ).id;

	const format = ( template, ...values ) => {
		let i = 0;
		return String( template || '' ).replace( /%(\d\$)?[sd]/g, ( match, position ) => String( values[ position ? parseInt( position, 10 ) - 1 : i++ ] ) );
	};

	// A variation's fields are named with its position on the page (WooCommerce: "price[3]") or,
	// in some plugins, with its ID ("warehouse_price[428][0][price]").
	function keysOf( $row ) {
		const $id = $row.find( 'input.variable_post_id' );
		const match = NAME.exec( $id.attr( 'name' ) || '' );
		return { loop: match ? match[ 2 ] : null, id: String( $id.val() || '' ) };
	}

	const contentOf = ( $row ) => $row.find( '.woocommerce_variable_attributes' )[ 0 ];

	// "#428 · 8GB, Core i5, 128GB" (an empty attribute shows "Any …").
	function labelOf( $row ) {
		const values = $row
			.children( 'h3' )
			.find( 'select' )
			.map( function () {
				return $( this ).find( 'option:selected' ).text().trim();
			} )
			.get()
			.filter( Boolean );

		return '#' + $row.find( 'input.variable_post_id' ).val() + ( values.length ? ' · ' + values.join( ', ' ) : '' );
	}

	/**
	 * Copyable fields of a variation, by field name without the variation index.
	 *
	 * A name the theme does not know that has more brackets after the index
	 * ("warehouse_price[3][0][price]": a list of rows added by a plugin) gets a
	 * group of its own ("x:" + name), other unknown names go to "other".
	 *
	 * @param {jQuery} $row
	 * @return {Map<string, {base: string, kind: string, group: string, keys: Map<string, HTMLElement[]>}>}
	 *   Key "name|loop" or "name|id"; keys: the rest of the name => its inputs.
	 */
	function fieldsOf( $row ) {
		const row = keysOf( $row );
		const fields = new Map();

		$( contentOf( $row ) )
			.find( ':input[name]' )
			.not( 'button, [type=button], [type=submit], [type=reset], [type=file]' )
			.each( function () {
				const match = NAME.exec( this.name );
				const kind = ! match ? null : match[ 2 ] === row.loop ? 'loop' : match[ 2 ] === row.id ? 'id' : null;
				if ( ! kind ) {
					return;
				}

				const base = match[ 1 ];
				if ( SKIP.includes( base ) || base.indexOf( '_wc_variation_file' ) === 0 ) {
					return;
				}

				let group = groupOf( base );
				if ( group === 'other' && /^\[[^\]]*\]./.test( match[ 3 ] ) ) {
					group = 'x:' + base;
				}

				const key = base + '|' + kind;
				if ( ! fields.has( key ) ) {
					fields.set( key, { base, kind, group, keys: new Map() } );
				}
				const keys = fields.get( key ).keys;
				if ( ! keys.has( match[ 3 ] ) ) {
					keys.set( match[ 3 ], [] );
				}
				keys.get( match[ 3 ] ).push( this );
			} );

		return fields;
	}

	const sameKeys = ( a, b ) => a.size === b.size && Array.from( a.keys() ).every( ( key ) => b.has( key ) && b.get( key ).length === a.get( key ).length );

	// Inputs of one field (base name and variation index) inside root.
	function inputsOf( root, base, index ) {
		return $( root )
			.find( ':input[name]' )
			.filter( function () {
				const match = NAME.exec( this.name );
				return !! match && match[ 1 ] === base && match[ 2 ] === index;
			} )
			.toArray();
	}

	// Holds a named input of another field.
	function hasOtherFields( node, base ) {
		return $( node )
			.find( ':input[name]' )
			.toArray()
			.some( ( input ) => {
				const match = NAME.exec( input.name );
				return ! match || match[ 1 ] !== base;
			} );
	}

	/**
	 * Block of a list field: the largest element around all its inputs that
	 * holds no other field (the table of rows, often with its title and
	 * "add" button).
	 *
	 * @param {HTMLElement}   root
	 * @param {HTMLElement[]} inputs
	 * @param {string}        base
	 * @return {HTMLElement|null}
	 */
	function blockOf( root, inputs, base ) {
		let node = inputs[ 0 ].parentNode;
		while ( node && node !== root && ! inputs.every( ( input ) => node.contains( input ) ) ) {
			node = node.parentNode;
		}
		if ( ! node || node === root || hasOtherFields( node, base ) ) {
			return null;
		}
		while ( node.parentNode && node.parentNode !== root && ! hasOtherFields( node.parentNode, base ) ) {
			node = node.parentNode;
		}
		return node;
	}

	const pathOf = ( node, root ) => {
		const path = [];
		for ( ; node && node !== root; node = node.parentNode ) {
			path.unshift( Array.prototype.indexOf.call( node.parentNode.children, node ) );
		}
		return path;
	};

	const nodeAt = ( root, path ) => path.reduce( ( node, index ) => ( node ? node.children[ index ] : null ), root ) || null;

	/**
	 * Copy a list field whose rows differ (another number of rows): the block
	 * of the source replaces the one of the target, renamed for the target.
	 *
	 * The block is found from whichever variation has rows, and in the other
	 * one at the same place (the variations are rendered from the same markup).
	 *
	 * @param {jQuery} $source
	 * @param {jQuery} $target
	 * @param {string} base
	 * @param {string} kind 'loop' or 'id'.
	 * @return {boolean} Copied.
	 */
	function replaceBlock( $source, $target, base, kind ) {
		const source = keysOf( $source );
		const target = keysOf( $target );
		const from = source[ kind ];
		const to = target[ kind ];
		const sourceRoot = contentOf( $source );
		const targetRoot = contentOf( $target );
		const sourceInputs = inputsOf( sourceRoot, base, from );
		const targetInputs = inputsOf( targetRoot, base, to );
		let sourceBlock = null;
		let targetBlock = null;

		if ( sourceInputs.length ) {
			sourceBlock = blockOf( sourceRoot, sourceInputs, base );
			targetBlock = sourceBlock && nodeAt( targetRoot, pathOf( sourceBlock, sourceRoot ) );
		} else if ( targetInputs.length ) {
			targetBlock = blockOf( targetRoot, targetInputs, base );
			sourceBlock = targetBlock && nodeAt( sourceRoot, pathOf( targetBlock, targetRoot ) );
		}

		if ( ! sourceBlock || ! targetBlock || sourceBlock.tagName !== targetBlock.tagName || hasOtherFields( sourceBlock, base ) || hasOtherFields( targetBlock, base ) ) {
			return false;
		}

		const clone = sourceBlock.cloneNode( true );
		const $clone = $( clone );

		// Names, and the attributes a plugin reads to name the rows added later ("data-loop", the variation ID).
		$clone.find( '[name]' ).each( function () {
			const match = NAME.exec( this.name );
			if ( match && match[ 1 ] === base && match[ 2 ] === from ) {
				this.name = base + '[' + to + ']' + match[ 3 ];
			}
		} );
		$clone.find( '*' ).addBack().each( function () {
			Array.from( this.attributes ).forEach( ( attribute ) => {
				const isData = attribute.name.indexOf( 'data-' ) === 0 || attribute.name === 'rel';
				if ( isData && attribute.value === source.id ) {
					attribute.value = target.id;
				} else if ( isData && attribute.name.indexOf( 'loop' ) > -1 && attribute.value === source.loop ) {
					attribute.value = target.loop;
				}
			} );
		} );

		// IDs stay unique (the source keeps its own); labels follow.
		$clone.find( '[id]' ).addBack( '[id]' ).each( function () {
			const id = this.id;
			this.id = id + '-' + target.id;
			$clone.find( 'label[for="' + id + '"]' ).attr( 'for', this.id );
		} );

		// A clone keeps the HTML, not what was typed or picked.
		const live = $( sourceBlock ).find( ':input' ).toArray();
		$clone.find( ':input' ).each( function ( index ) {
			const input = live[ index ];
			if ( ! input ) {
				return;
			}
			if ( input.type === 'checkbox' || input.type === 'radio' ) {
				this.checked = input.checked;
			} else if ( input.tagName === 'SELECT' ) {
				Array.from( input.options ).forEach( ( option, i ) => {
					if ( this.options[ i ] ) {
						this.options[ i ].selected = option.selected;
					}
				} );
			} else {
				this.value = input.value;
			}
		} );

		// Enhanced selects (select2 / selectWoo) are drawn again by WooCommerce.
		$clone.find( '.select2-container' ).remove();
		$clone.find( 'select' ).removeClass( 'select2-hidden-accessible enhanced' ).removeAttr( 'data-select2-id aria-hidden tabindex' );
		$clone.find( '.hasDatepicker' ).removeClass( 'hasDatepicker' );

		targetBlock.parentNode.replaceChild( clone, targetBlock );
		$( document.body ).trigger( 'wc-enhanced-select-init' );
		$clone.find( ':input[name]' ).first().trigger( 'change' );

		return true;
	}

	// Title of a list field for the dialog: the text just before its block, else its name.
	function titleOf( $row, base, kind ) {
		const root = contentOf( $row );
		const inputs = inputsOf( root, base, keysOf( $row )[ kind ] );
		const block = inputs.length ? blockOf( root, inputs, base ) : null;
		const short = ( node ) => {
			const text = node && ! $( node ).find( ':input' ).length ? $( node ).text().replace( /\s+/g, ' ' ).trim() : '';
			return text.length <= 80 ? text : '';
		};

		if ( block ) {
			const $first = $( block ).find( 'legend, h1, h2, h3, h4, h5, h6, label, strong, p' ).first();
			for ( let node = block.previousElementSibling, i = 0; node && i < 2; node = node.previousElementSibling, i++ ) {
				if ( short( node ) ) {
					return short( node );
				}
			}
			if ( $first.length && ! $first.closest( 'th, td' ).length && short( $first[ 0 ] ) ) {
				return short( $first[ 0 ] );
			}
		}
		return base;
	}

	// One field (all its inputs: radios, checkboxes of a list...). Returns true when something changed.
	function copyField( sources, targets ) {
		let changed = false;

		sources.forEach( ( source, index ) => {
			const target = targets[ index ];
			if ( ! target ) {
				return;
			}

			if ( source.type === 'checkbox' || source.type === 'radio' ) {
				if ( target.checked !== source.checked ) {
					target.checked = source.checked;
					$( target ).trigger( 'change' );
					changed = true;
				}
				return;
			}

			const value = $( source ).val();
			if ( JSON.stringify( $( target ).val() ) !== JSON.stringify( value ) ) {
				$( target ).val( value ).trigger( 'change' );
				changed = true;
			}
		} );

		return changed;
	}

	// The "Schedule" link and the date fields follow the copied dates (WooCommerce's own handlers).
	function syncSaleSchedule( $row ) {
		const $dates = $row.find( '.sale_price_dates_fields input' );
		const hasDates = $dates.toArray().some( ( input ) => input.value !== '' );
		const $open = $row.find( 'a.sale_schedule' );
		const isOpen = $open.length && $open[ 0 ].style.display === 'none';

		if ( hasDates && ! isOpen ) {
			$open.trigger( 'click' );
		} else if ( ! hasDates && isOpen ) {
			$row.find( 'a.cancel_sale_schedule' ).trigger( 'click' );
		}
	}

	/**
	 * Variation image and gallery.
	 *
	 * WooCommerce's gallery script (variation-gallery.js) keeps the hidden
	 * inputs, the main picture, the count and the variation image in step with
	 * the thumbnail list, but it is private. The source thumbnails are copied
	 * (they carry their image URLs), then removing an extra empty thumbnail
	 * makes that script rebuild the whole field from the list.
	 *
	 * @param {jQuery} $source
	 * @param {jQuery} $target
	 * @return {boolean} Something changed.
	 */
	function copyImages( $source, $target ) {
		let changed = false;
		const $sourceGallery = $source.find( '.wc-variation-gallery-field' );
		const $targetGallery = $target.find( '.wc-variation-gallery-field' );

		if ( $sourceGallery.length && $targetGallery.length ) {
			const ids = $sourceGallery.find( '.wc-variation-gallery-image-ids' ).val();
			if ( $targetGallery.find( '.wc-variation-gallery-image-ids' ).val() !== ids ) {
				const $list = $targetGallery.find( '.wc-variation-gallery-field__thumbs' );
				const $extra = $( '<li class="wc-variation-gallery-thumb" data-attachment_id="0"><button type="button" class="wc-variation-gallery-thumb__remove"></button></li>' );

				$targetGallery.find( '.wc-variation-gallery-field__hero' ).attr( 'data-active-index', 0 );
				$list.empty().append( $sourceGallery.find( '.wc-variation-gallery-thumb' ).clone().removeClass( 'is-dragging' ), $extra );
				$extra.children( 'button' ).trigger( 'click' );
				changed = true;
			}
		} else {
			// Without the gallery: the variation image only.
			const $sourceInput = $source.find( 'input.upload_image_id' );
			const $targetInput = $target.find( 'input.upload_image_id' );

			if ( $sourceInput.length && $targetInput.length && $targetInput.val() !== $sourceInput.val() ) {
				const $sourceButton = $source.find( 'a.upload_image_button' );
				const $targetButton = $target.find( 'a.upload_image_button' );

				$targetButton.toggleClass( 'remove', $sourceButton.hasClass( 'remove' ) ).attr( 'data-tip', $sourceButton.attr( 'data-tip' ) );
				$targetButton.find( 'img' ).first().attr( 'src', $sourceButton.find( 'img' ).first().attr( 'src' ) );
				$targetInput.val( $sourceInput.val() ).trigger( 'change' );
				changed = true;
			}
		}

		// Theme "Additional images" (silver-variation-images), when enabled.
		const $sourceExtra = $source.find( 'input.ux-additional-variation-images-save' );
		const $targetExtra = $target.find( 'input.ux-additional-variation-images-save' );

		if ( $sourceExtra.length && $targetExtra.length && $targetExtra.val() !== $sourceExtra.val() ) {
			const $list = $targetExtra.siblings( 'ul.ux-additional-variation-images__list' );

			$list.empty().append( $sourceExtra.siblings( 'ul.ux-additional-variation-images__list' ).children().clone() );
			if ( $list.data( 'ui-sortable' ) ) {
				$list.sortable( 'refresh' );
			}
			$targetExtra.val( $sourceExtra.val() ).trigger( 'change' );
			changed = true;
		}

		return changed;
	}

	/**
	 * Copy the chosen groups of $source into $target.
	 *
	 * @param {jQuery}   $source
	 * @param {jQuery}   $target
	 * @param {string[]} groups
	 * @return {boolean} Something changed.
	 */
	function syncRow( $source, $target, groups ) {
		const sourceFields = fieldsOf( $source );
		const targetFields = fieldsOf( $target );
		const empty = new Map();
		let changed = false;

		// Fields of both variations: a list the source has no row of is emptied in the target too.
		new Set( [ ...sourceFields.keys(), ...targetFields.keys() ] ).forEach( ( key ) => {
			const field = sourceFields.get( key ) || targetFields.get( key );
			if ( field.group === 'image' || ! groups.includes( field.group ) ) {
				return;
			}

			const sourceKeys = sourceFields.has( key ) ? sourceFields.get( key ).keys : empty;
			const targetKeys = targetFields.has( key ) ? targetFields.get( key ).keys : empty;

			if ( sameKeys( sourceKeys, targetKeys ) ) {
				sourceKeys.forEach( ( inputs, rest ) => {
					changed = copyField( inputs, targetKeys.get( rest ) ) || changed;
				} );
			} else {
				changed = replaceBlock( $source, $target, field.base, field.kind ) || changed;
			}
		} );

		if ( groups.includes( 'image' ) ) {
			changed = copyImages( $source, $target ) || changed;
		}
		if ( groups.includes( 'sale_price' ) ) {
			syncSaleSchedule( $target );
		}
		// For code of its own: $( document.body ).on( 'silver_variation_synced', ( event, $source, $target, groups ) => {} ).
		$( document.body ).trigger( 'silver_variation_synced', [ $source, $target, groups ] );

		if ( changed ) {
			// A copied image already marks the row; a row can also change without a field event (gallery rebuild).
			$target.addClass( 'variation-needs-update' );
			$( 'button.cancel-variation-changes, button.save-variation-changes' ).prop( 'disabled', false );
		}

		return changed;
	}

	function savedGroups() {
		try {
			const saved = JSON.parse( window.localStorage.getItem( STORAGE_KEY ) || 'null' );
			return Array.isArray( saved ) ? saved : null;
		} catch ( e ) {
			return null;
		}
	}

	function saveGroups( groups ) {
		try {
			window.localStorage.setItem( STORAGE_KEY, JSON.stringify( groups ) );
		} catch ( e ) {
			// Private mode: the defaults are used next time.
		}
	}

	const checkbox = ( value, checked, text ) =>
		$( '<label></label>' ).append( $( '<input type="checkbox">' ).val( value ).prop( 'checked', checked ), document.createTextNode( text ) );

	// Content of the dialog: groups found in the variations, then the other variations.
	function buildForm( $source, $others ) {
		const present = new Set();
		const lists = new Map(); // "x:name" => title, for the lists added by plugins.

		$source.add( $others ).each( function () {
			const $row = $( this );
			fieldsOf( $row ).forEach( ( field ) => {
				present.add( field.group );
				if ( field.group.indexOf( 'x:' ) === 0 && ! lists.has( field.group ) ) {
					lists.set( field.group, titleOf( $row, field.base, field.kind ) );
				}
			} );
		} );
		if ( $source.find( '.wc-variation-gallery-field, input.upload_image_id' ).length ) {
			present.add( 'image' );
		}

		const saved = savedGroups();
		const $form = $( '<div class="silver-vsync"></div>' );
		const $lead = $( '<p></p>' );
		const from = format( i18n.from, '\u0000' ).split( '\u0000' );

		$lead.append( document.createTextNode( from[ 0 ] || '' ), $( '<strong></strong>' ).text( labelOf( $source ) ), document.createTextNode( from[ 1 ] || '' ) );

		const $groups = $( '<div class="silver-vsync__groups"></div>' );
		const known = GROUPS.filter( ( group ) => group.id !== 'other' );
		const extra = Array.from( lists, ( [ id, title ] ) => ( { id, on: false, title } ) );
		[ ...known, ...extra, GROUPS.find( ( group ) => group.id === 'other' ) ]
			.filter( ( group ) => present.has( group.id ) )
			.forEach( ( group ) => {
				$groups.append( checkbox( group.id, saved ? saved.includes( group.id ) : group.on, group.title || groupLabels[ group.id ] || group.id ) );
			} );

		const $all = $( '<input type="checkbox" checked>' );
		const $targets = $( '<div class="silver-vsync__targets"></div>' );
		$others.each( function () {
			$targets.append( checkbox( $( this ).find( 'input.variable_post_id' ).val(), true, labelOf( $( this ) ) ) );
		} );

		$all.on( 'change', () => $targets.find( 'input' ).prop( 'checked', $all.prop( 'checked' ) ) );
		$targets.on( 'change', 'input', () => {
			const $inputs = $targets.find( 'input' );
			$all.prop( 'checked', $inputs.filter( ':checked' ).length === $inputs.length );
		} );

		$form.append(
			$lead,
			$( '<fieldset></fieldset>' ).append( $( '<legend></legend>' ).text( i18n.data || '' ), $groups ),
			$( '<fieldset></fieldset>' ).append(
				$( '<legend></legend>' ).append( $( '<span></span>' ).text( i18n.targets || '' ), $( '<label></label>' ).append( $all, document.createTextNode( i18n.all || '' ) ) ),
				$targets
			)
		);

		const total = parseInt( $( ROOT ).find( '.woocommerce_variations' ).attr( 'data-total' ), 10 );
		const shown = $others.length + 1;
		if ( total > shown ) {
			$form.append( $( '<p class="silver-vsync__note"></p>' ).text( format( i18n.pageOnly, shown, total ) ) );
		}

		$form.append( $( '<p class="silver-vsync__muted"></p>' ).text( i18n.notes || '' ), $( '<p class="silver-vsync__error" role="alert"></p>' ) );

		return $form;
	}

	function open( $source ) {
		const dialog = window.silverDialog;
		const $rows = $( ROOT ).find( '.woocommerce_variations .woocommerce_variation' );
		const $others = $rows.not( $source );

		if ( ! dialog ) {
			return;
		}
		if ( ! $others.length ) {
			dialog.alert( i18n.noOther || '', { type: 'info' } );
			return;
		}

		const $form = buildForm( $source, $others );
		const $error = $form.find( '.silver-vsync__error' );

		dialog.confirm( $form[ 0 ], {
			title: i18n.title,
			yesText: i18n.apply,
			noText: i18n.cancel,
			className: 'silver-vsync-dialog',
			onYes( close, setLoading ) {
				const groups = $form.find( '.silver-vsync__groups input:checked' ).map( ( i, input ) => input.value ).get();
				const ids = $form.find( '.silver-vsync__targets input:checked' ).map( ( i, input ) => input.value ).get();

				if ( ! groups.length || ! ids.length ) {
					$error.text( ! groups.length ? i18n.pickData : i18n.pickTarget );
					setLoading( false );
					return;
				}

				saveGroups( groups );

				const $changed = $others
					.filter( function () {
						return ids.includes( String( $( this ).find( 'input.variable_post_id' ).val() ) );
					} )
					.filter( function () {
						return syncRow( $source, $( this ), groups );
					} );

				close();

				$changed.removeClass( 'silver-vsync-flash' );
				void document.body.offsetWidth; // Restart the highlight.
				$changed.addClass( 'silver-vsync-flash' ).one( 'animationend', function () {
					$( this ).removeClass( 'silver-vsync-flash' );
				} );

				dialog.alert( $changed.length ? format( i18n.done, $changed.length ) : i18n.same, {
					type: $changed.length ? 'success' : 'info',
					timeout: 4000,
				} );
			},
		} );
	}

	// Button next to "Edit" in each variation header.
	function addButtons() {
		$( ROOT )
			.find( '.woocommerce_variation > h3' )
			.each( function () {
				const $h3 = $( this );
				if ( $h3.children( '.silver-vsync-btn' ).length ) {
					return;
				}

				$( '<button type="button" class="silver-vsync-btn"></button>' )
					.text( i18n.button || 'Sync' )
					.attr( 'title', i18n.buttonTitle || '' )
					.insertAfter( $h3.children( '.edit_variation' ) );
			} );
	}

	// Capture phase: the click never reaches WooCommerce's header handlers, which would fold the variation.
	document.addEventListener(
		'click',
		( event ) => {
			const button = event.target instanceof Element ? event.target.closest( ROOT + ' .silver-vsync-btn' ) : null;
			if ( ! button ) {
				return;
			}
			event.preventDefault();
			event.stopPropagation();
			open( $( button ).closest( '.woocommerce_variation' ) );
		},
		true
	);

	$( document.body ).on( 'woocommerce_variations_loaded woocommerce_variations_added', addButtons );
	$( addButtons );
} )( window.jQuery, window.silverVariationSync );
