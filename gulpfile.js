var gulp         = require( 'gulp' ),
	less         = require( 'gulp-less' ),
	plumber      = require( 'gulp-plumber' ),
	path         = require( 'path' ),
	notify       = require( 'gulp-notify' ),
	cleanCSS     = require( 'gulp-clean-css' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	assetsDir    = 'src/Boxtal/BoxtalWoocommerce/assets';

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

// Styles task for local wordpress
gulp.task(
	'css', function () {
		return gulp.src( [assetsDir + '/less/*.less'] )
		.pipe( plumber( {errorHandler: onError} ) )
		.pipe(
			less(
				{
					paths: [path.join( __dirname, 'less', 'includes' )]
				}
			)
		)
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
