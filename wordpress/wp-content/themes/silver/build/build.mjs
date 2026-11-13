/**
 * Silver theme asset build.
 *
 * Edit the readable sources in assets/css/src and assets/js/src, then run:
 *   cd wp-content/themes/silver/build && npm install && npm run build
 *
 * A source is written minified under the same name (src/x.css -> x.css),
 * except the parts of a bundle (see `bundles`), which are merged into the
 * main file instead.
 */
import { readFileSync, writeFileSync, readdirSync, statSync } from 'node:fs';
import { join, relative, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { gzipSync } from 'node:zlib';
import { transform as cssTransform } from 'lightningcss';
import { transformSync as jsTransform } from 'esbuild';

const root = join( dirname( fileURLToPath( import.meta.url ) ), '..' );
const v = ( major, minor = 0 ) => ( major << 16 ) | ( minor << 8 );

// Browsers supported: Chrome/Edge 90+, Firefox 88+, Safari/iOS 15+ (2021+).
// Older Safari targets make lightningcss expand logical properties into long :lang() selectors.
const cssTargets = { chrome: v( 90 ), edge: v( 90 ), firefox: v( 88 ), safari: v( 15 ), ios_saf: v( 15 ), samsung: v( 15 ) };
const jsTarget = [ 'chrome90', 'edge90', 'firefox88', 'safari15' ];

// Element / component files merged into the main CSS and JS, in this order.
// To add one: create it in src and list it here.
const cssParts = [ 'silver-counter.css', 'silver-dialog.css', 'silver-mega-menu.css' ];
const jsParts = [ 'silver-swiper.js', 'silver-dialog.js', 'silver-counter.js', 'silver-mega-menu.js' ];

// Bundles: output file => parts joined in order. A part is minified like any
// other source and is not written as a file of its own (see `standalone`).
// `raw` marks a compiled file that has no readable source (the theme's webpack
// bundle, the RTL stylesheet): it is copied as it is, never minified again.
const bundles = {
	css: {
		'silver.css': [ 'silver.css', ...cssParts ],
		'silver-rtl.css': [ { file: 'silver-rtl.css', raw: true }, ...cssParts ],
	},
	js: {
		'silver.js': [ { file: 'silver.js', raw: true }, ...jsParts ],
	},
};

// Parts that are also written on their own: wp-admin loads the dialog without
// silver.css / silver.js.
const standalone = [ 'silver-dialog.css', 'silver-dialog.js' ];

const compilers = {
	css( src ) {
		const { code, warnings } = cssTransform( {
			filename: src,
			code: readFileSync( src ),
			minify: true,
			targets: cssTargets,
			errorRecovery: true,
		} );
		warnings.forEach( ( w ) => console.warn( `  warn ${ relative( root, src ) }:${ w.loc.line } ${ w.message }` ) );
		return code;
	},
	js( src ) {
		// No "format": top-level names stay global, as in the original scripts.
		const { code } = jsTransform( readFileSync( src, 'utf8' ), {
			loader: 'js',
			minify: true,
			target: jsTarget,
			legalComments: 'none',
		} );
		return Buffer.from( code );
	},
};

const walk = ( dir ) => readdirSync( dir ).flatMap( ( name ) => {
	const p = join( dir, name );
	return statSync( p ).isDirectory() ? walk( p ) : [ p ];
} );

const kb = ( n ) => ( n / 1024 ).toFixed( 1 ).padStart( 6 ) + 'KB';
const report = ( before, out, code ) => {
	console.log( `${ relative( root, out ).padEnd( 40 ) } ${ kb( before ) } -> ${ kb( code.length ) }  (gzip ${ kb( gzipSync( code ).length ) })` );
};

// Same line endings on every platform (Git may check the raw files out as CRLF).
const readRaw = ( src ) => Buffer.from( readFileSync( src, 'utf8' ).replace( /\r\n/g, '\n' ).trimEnd() + '\n' );

let failed = false;

for ( const type of [ 'css', 'js' ] ) {
	const srcDir = join( root, 'assets', type, 'src' );
	const outDir = join( root, 'assets', type );
	const slash = ( path ) => path.replace( /\\/g, '/' );
	const part = ( entry ) => ( typeof entry === 'string' ? { file: entry, raw: false } : entry );

	// Compiled once, also when a part belongs to several bundles.
	const cache = new Map();
	const compile = ( file, raw ) => {
		if ( ! cache.has( file ) ) {
			const src = join( srcDir, file );
			cache.set( file, raw ? readRaw( src ) : compilers[ type ]( src ) );
		}
		return cache.get( file );
	};

	const bundled = new Set( Object.values( bundles[ type ] ).flat().map( ( entry ) => part( entry ).file ) );
	const rawFiles = new Set( Object.values( bundles[ type ] ).flat().map( part ).filter( ( entry ) => entry.raw ).map( ( entry ) => entry.file ) );

	// Sources written under their own name.
	for ( const src of walk( srcDir ).filter( ( f ) => f.endsWith( '.' + type ) ) ) {
		const file = slash( relative( srcDir, src ) );
		if ( bundled.has( file ) && ! standalone.includes( file ) ) {
			continue;
		}
		try {
			const code = compile( file, rawFiles.has( file ) );
			writeFileSync( join( outDir, file ), code );
			report( readFileSync( src ).length, join( outDir, file ), code );
		} catch ( e ) {
			failed = true;
			console.error( `ERROR ${ relative( root, src ) }: ${ e.message }` );
		}
	}

	// Bundles.
	for ( const [ output, entries ] of Object.entries( bundles[ type ] ) ) {
		try {
			const parts = entries.map( part );
			const before = parts.reduce( ( total, entry ) => total + readFileSync( join( srcDir, entry.file ) ).length, 0 );
			const code = Buffer.concat( parts.map( ( entry ) => {
				const compiled = compile( entry.file, entry.raw );
				// Every part ends with a new line, so that a part never runs into the next one.
				return compiled[ compiled.length - 1 ] === 10 ? compiled : Buffer.concat( [ compiled, Buffer.from( '\n' ) ] );
			} ) );
			writeFileSync( join( outDir, output ), code );
			report( before, join( outDir, output ), code );
			console.log( `  = ${ parts.map( ( entry ) => entry.file ).join( ' + ' ) }` );
		} catch ( e ) {
			failed = true;
			console.error( `ERROR bundle ${ output }: ${ e.message }` );
		}
	}
}

process.exit( failed ? 1 : 0 );
