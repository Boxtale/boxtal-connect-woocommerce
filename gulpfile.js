var gulp         = require( 'gulp' ),
	less         = require( 'gulp-less' ),
	plumber      = require( 'gulp-plumber' ),
	path         = require( 'path' ),
    uglify 		 = require('gulp-uglify-es').default,
    rename 		 = require('gulp-rename'),
	notify       = require( 'gulp-notify' ),
	cleanCSS     = require( 'gulp-clean-css' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
    concat 		 = require('gulp-concat'),
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
    return gulp.src([assetsDir + '/js/*.js', '!' + assetsDir + '/js/*.min.js'])
		.pipe(babel({ presets: ['es2015-ie', 'es2015', 'stage-3'] }))
        .pipe(plumber( {errorHandler: onError} ))
        .pipe(uglify())
        .pipe(rename({ suffix: '.min' }))
        .pipe(gulp.dest(assetsDir + '/js'));
});

// Styles task for local wordpress
gulp.task(
	'css', function () {
		return gulp.src( [assetsDir + '/less/*.less'] )
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
		.pipe( cleanCSS( {compatibility: 'ie8'} ) )
		.pipe( gulp.dest( assetsDir + '/css' ) );
	}
);
