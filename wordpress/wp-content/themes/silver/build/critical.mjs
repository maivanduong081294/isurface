/**
 * Critical CSS of the theme stylesheets.
 *
 * Opens the pages of critical.config.json in headless Chrome at each screen
 * size, keeps the rules of the theme stylesheets that style an element of
 * the first screen (or hide one there: dropdowns, drawers), and writes them,
 * in the order of the original file, to assets/css/critical/<name>.css.
 *
 * Advanced > Performance > Critical CSS prints these files in the page and
 * loads the full stylesheets without blocking the display. The full files
 * stay as they are: they apply again, in their order, once loaded.
 *
 * Run it again after changing the CSS or the design of the first screen:
 *   cd wp-content/themes/silver/build && npm install && npm run critical
 *
 * Chrome: CHROME_PATH, else the usual install paths.
 */
import { readFileSync, writeFileSync, mkdirSync, existsSync, statSync } from 'node:fs';
import { join, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { gzipSync } from 'node:zlib';
import { createHash } from 'node:crypto';
import puppeteer from 'puppeteer-core';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );
const config = JSON.parse( readFileSync( join( dirname( fileURLToPath( import.meta.url ) ), 'critical.config.json' ), 'utf8' ) );
const outDir = join( root, 'assets', 'css', 'critical' );

// Stylesheets handled: name => file in assets/css (the handle is in the PHP side).
const sheets = config.stylesheets;

const chromePaths = [
	process.env.CHROME_PATH,
	'C:/Program Files/Google/Chrome/Application/chrome.exe',
	'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
	'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
	'/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
	'/usr/bin/google-chrome',
	'/usr/bin/chromium',
	'/usr/bin/chromium-browser',
].filter( Boolean );
const executablePath = chromePaths.find( ( p ) => existsSync( p ) );
if ( ! executablePath ) {
	console.error( 'Chrome not found: set CHROME_PATH.' );
	process.exit( 1 );
}

/**
 * Runs in the page: the rules of each handled stylesheet that apply to the
 * first screen. Returns { name: { id: { media: [...], selectors: [...], body } } }.
 */
function collectInPage( sheetFiles, foldHeight ) {
	const fold = foldHeight;

	// Splits a selector list on the top-level commas.
	const splitSelectors = ( text ) => {
		const out = [];
		let depth = 0;
		let current = '';
		for ( const ch of text ) {
			if ( ch === '(' || ch === '[' ) depth++;
			if ( ch === ')' || ch === ']' ) depth--;
			if ( ch === ',' && depth === 0 ) {
				out.push( current.trim() );
				current = '';
			} else {
				current += ch;
			}
		}
		if ( current.trim() ) out.push( current.trim() );
		return out;
	};

	// The selector without what querySelectorAll can not match or what
	// depends on the visitor (pseudo-elements, hover, focus...).
	const matchable = ( selector ) => {
		const s = selector
			.replace( /::?(before|after|first-letter|first-line|placeholder|selection|marker|backdrop|-webkit-[a-z-]+|-moz-[a-z-]+|-ms-[a-z-]+)(\([^)]*\))?/gi, '' )
			.replace( /:(hover|focus|focus-visible|focus-within|active|visited|target|checked|indeterminate|invalid|valid|disabled|enabled|read-only|placeholder-shown|autofill)\b/gi, '' )
			.trim();
		return s === '' || /[>+~]$/.test( s ) ? '*' : s;
	};

	// An element of the first screen: its box starts above the fold. A
	// hidden element (no box) counts by its nearest ancestor with a box, so
	// the rules that keep a header dropdown hidden are kept too.
	const inFold = ( el ) => {
		for ( let node = el; node && node.nodeType === 1; node = node.parentElement ) {
			if ( node === document.documentElement || node === document.body ) return true;
			const r = node.getBoundingClientRect();
			if ( r.width || r.height ) return r.top < fold;
		}
		return false;
	};

	const matchesFold = ( selector ) => {
		let list;
		try {
			list = document.querySelectorAll( matchable( selector ) );
		} catch ( e ) {
			return false;
		}
		for ( const el of list ) {
			if ( inFold( el ) ) return true;
		}
		return false;
	};

	const result = {};
	for ( const [ name, file ] of Object.entries( sheetFiles ) ) {
		const sheet = [ ...document.styleSheets ].find( ( s ) => s.href && s.href.split( '?' )[ 0 ].endsWith( '/assets/css/' + file ) );
		if ( ! sheet ) continue;
		const kept = {};

		const walk = ( rules, path, media ) => {
			[ ...rules ].forEach( ( rule, index ) => {
				const id = path.concat( index );
				if ( rule instanceof CSSMediaRule ) {
					if ( window.matchMedia( rule.media.mediaText ).matches ) {
						walk( rule.cssRules, id, media.concat( '@media ' + rule.media.mediaText ) );
					}
				} else if ( rule instanceof CSSSupportsRule ) {
					if ( CSS.supports( rule.conditionText ) ) {
						walk( rule.cssRules, id, media.concat( '@supports ' + rule.conditionText ) );
					}
				} else if ( rule instanceof CSSStyleRule ) {
					const selectors = splitSelectors( rule.selectorText ).filter( matchesFold );
					if ( selectors.length ) {
						kept[ id.join( '.' ) ] = { media, selectors, body: rule.style.cssText };
					}
				} else if ( rule instanceof CSSFontFaceRule ) {
					kept[ id.join( '.' ) ] = { media, raw: rule.cssText };
				}
			} );
		};
		walk( sheet.cssRules, [], [] );
		result[ name ] = kept;
	}
	return result;
}

const browser = await puppeteer.launch( { executablePath, headless: 'new', args: [ '--ignore-certificate-errors' ] } );
const merged = Object.fromEntries( Object.keys( sheets ).map( ( name ) => [ name, {} ] ) );

for ( const viewport of config.viewports ) {
	for ( const path of config.urls ) {
		const page = await browser.newPage();
		await page.setViewport( viewport );
		const url = config.base.replace( /\/$/, '' ) + path;
		await page.goto( url, { waitUntil: 'networkidle2', timeout: 60000 } );
		const found = await page.evaluate( collectInPage, sheets, viewport.height );
		let count = 0;
		for ( const [ name, rules ] of Object.entries( found ) ) {
			for ( const [ id, rule ] of Object.entries( rules ) ) {
				const current = merged[ name ][ id ];
				if ( current && rule.selectors ) {
					rule.selectors.forEach( ( s ) => current.selectors.includes( s ) || current.selectors.push( s ) );
				} else if ( ! current ) {
					merged[ name ][ id ] = rule;
				}
				count++;
			}
		}
		console.log( `${ String( viewport.width ).padStart( 4 ) }px ${ path.padEnd( 24 ) } ${ count } rules` );
		await page.close();
	}
}
await browser.close();

// Original order: ids are the rule positions (1.4.2 = rule 2 of rule 4 of rule 1).
const byPosition = ( a, b ) => {
	const x = a.split( '.' ).map( Number );
	const y = b.split( '.' ).map( Number );
	for ( let i = 0; i < Math.max( x.length, y.length ); i++ ) {
		if ( ( x[ i ] ?? -1 ) !== ( y[ i ] ?? -1 ) ) return ( x[ i ] ?? -1 ) - ( y[ i ] ?? -1 );
	}
	return 0;
};

mkdirSync( outDir, { recursive: true } );
const manifest = {};
let totalGzip = 0;

for ( const [ name, file ] of Object.entries( sheets ) ) {
	const ids = Object.keys( merged[ name ] ).sort( byPosition );
	let css = '';
	let open = [];
	for ( const id of ids ) {
		const rule = merged[ name ][ id ];
		// Close / open the @media / @supports blocks that differ.
		let same = 0;
		while ( same < open.length && same < rule.media.length && open[ same ] === rule.media[ same ] ) same++;
		css += '}'.repeat( open.length - same );
		for ( const block of rule.media.slice( same ) ) css += block + '{';
		open = rule.media;
		css += rule.raw ? rule.raw : rule.selectors.join( ',' ) + '{' + rule.body + '}';
	}
	css += '}'.repeat( open.length );

	const source = join( root, 'assets', 'css', file );
	writeFileSync( join( outDir, file ), css );
	const gzip = gzipSync( css ).length;
	totalGzip += gzip;
	manifest[ name ] = {
		file,
		source_size: statSync( source ).size,
		source_md5: createHash( 'md5' ).update( readFileSync( source ) ).digest( 'hex' ),
		rules: ids.length,
	};
	console.log( `critical/${ file.padEnd( 20 ) } ${ ( css.length / 1024 ).toFixed( 1 ).padStart( 6 ) }KB  gzip ${ ( gzip / 1024 ).toFixed( 1 ) }KB  (${ ids.length } rules)` );
}

writeFileSync( join( outDir, 'manifest.json' ), JSON.stringify( { generated: new Date().toISOString(), base: config.base, urls: config.urls, sheets: manifest }, null, '\t' ) + '\n' );
console.log( `total gzip ${ ( totalGzip / 1024 ).toFixed( 1 ) }KB` + ( totalGzip > 14 * 1024 ? '  (over 14KB: check the pages / screens in critical.config.json)' : '' ) );
