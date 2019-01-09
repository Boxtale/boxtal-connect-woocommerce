var gulp         = require( 'gulp' ),
	less         = require( 'gulp-less' ),
	plumber      = require( 'gulp-plumber' ),
	terser 		 = require('gulp-terser'),
    rename 		 = require('gulp-rename'),
	notify       = require( 'gulp-notify' ),
	cleanCSS     = require( 'gulp-clean-css' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	mergeStream  = require( 'merge-stream' ),
	assetsDir    = 'src/Boxtal/BoxtalConnectWoocommerce/assets',
	babel 		 = require('gulp-babel');

/* Error Notification
 ================================================================================== */
var onError = function (err) {
	notify.onError(
		{
			title: "Oops, some error:",
			message: "<%= error.message %>"
		}
	)( err );
	this.emit( 'end' );
};

// JS concat & minify task for local wordpress
gulp.task('js', function () {
    return mergeStream(
			gulp.src(
			[
				assetsDir + '/js/*.js',
				'!' + assetsDir + '/js/polyfills.js',
				'!' + assetsDir + '/js/*.min.js'
			])
			.pipe(babel({ presets: ['es2015'] }))
			.pipe(plumber( {errorHandler: onError} ))
			.pipe(terser({
				ie8: true
			}))
			.pipe(rename({ suffix: '.min' })),
			gulp.src(
			[
				assetsDir + '/js/polyfills.js',
				'node_modules/mapbox-gl/dist/mapbox-gl.js'
			])
			.pipe(terser({
				ie8: true,
				compress: false
			}))
			.pipe(rename({ suffix: '.min' })),
			gulp.src(
			[
				'node_modules/tail.select/js/tail.select-full.min.js'
			]),
			gulp.src(
			[
				'node_modules/promise-polyfill/dist/polyfill.min.js'
			])
			.pipe(rename("promise-polyfill.min.js"))
		)
        .pipe(gulp.dest(assetsDir + '/js'));
});

// Styles task for local wordpress
gulp.task(
	'css', function () {
		return mergeStream(
			gulp.src( [assetsDir + '/less/*.less'] )
			.pipe( plumber( {errorHandler: onError} ) )
			.pipe( less() )
			.pipe(
				autoprefixer(
					{
						browsers: ['last 2 versions', '>1%', 'ie 9'],
						cascade: false
					}
				)
			)
			.pipe( cleanCSS( {compatibility: 'ie8'} ) ),
			gulp.src(
				[
					'node_modules/mapbox-gl/dist/mapbox-gl.css',
					'node_modules/tail.select/css/tail.select-bootstrap3.css'
				]
			)
		)
		.pipe( gulp.dest( assetsDir + '/css' ) );
	}
);
