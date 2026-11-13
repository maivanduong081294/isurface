/**
 * Silver theme asset build.
 *
 * Edit the readable sources in assets/css/src and assets/js/src, then run:
 *   cd wp-content/themes/silver/build && npm install && npm run build
 *
 * Output keeps the original file names/paths, so enqueue code does not change.
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

const walk = ( dir ) => readdirSync( dir ).flatMap( ( name ) => {
	const p = join( dir, name );
	return statSync( p ).isDirectory() ? walk( p ) : [ p ];
} );

const kb = ( n ) => ( n / 1024 ).toFixed( 1 ).padStart( 6 ) + 'KB';
const report = ( src, out, code ) => {
	const before = readFileSync( src ).length;
	console.log( `${ relative( root, out ).padEnd( 40 ) } ${ kb( before ) } -> ${ kb( code.length ) }  (gzip ${ kb( gzipSync( code ).length ) })` );
};

let failed = false;

for ( const src of walk( join( root, 'assets/css/src' ) ).filter( ( f ) => f.endsWith( '.css' ) ) ) {
	const out = join( root, 'assets/css', relative( join( root, 'assets/css/src' ), src ) );
	try {
		const { code, warnings } = cssTransform( {
			filename: src,
			code: readFileSync( src ),
			minify: true,
			targets: cssTargets,
			errorRecovery: true,
		} );
		warnings.forEach( ( w ) => console.warn( `  warn ${ relative( root, src ) }:${ w.loc.line } ${ w.message }` ) );
		writeFileSync( out, code );
		report( src, out, code );
	} catch ( e ) {
		failed = true;
		console.error( `ERROR ${ relative( root, src ) }: ${ e.message }` );
	}
}

for ( const src of walk( join( root, 'assets/js/src' ) ).filter( ( f ) => f.endsWith( '.js' ) ) ) {
	const out = join( root, 'assets/js', relative( join( root, 'assets/js/src' ), src ) );
	try {
		// No "format": top-level names stay global, as in the original scripts.
		const { code } = jsTransform( readFileSync( src, 'utf8' ), {
			loader: 'js',
			minify: true,
			target: jsTarget,
			legalComments: 'none',
		} );
		writeFileSync( out, code );
		report( src, out, Buffer.from( code ) );
	} catch ( e ) {
		failed = true;
		console.error( `ERROR ${ relative( root, src ) }: ${ e.message }` );
	}
}

process.exit( failed ? 1 : 0 );
