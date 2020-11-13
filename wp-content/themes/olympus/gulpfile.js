/*===========GULP==============*/


var gulp = require('gulp'),
	sass = require('gulp-sass'),
	plumber = require('gulp-plumber'),
	autoprefixer = require('gulp-autoprefixer'),
	cssmin = require('gulp-cssmin'), // for minimizing css-files
	runSequence = require('run-sequence');

/*===========Compile SCSS==============*/


gulp.task('sass', function() {

	gulp.src('sass/*.scss')
		.pipe(plumber())
		.pipe(sass(
			{
				linefeed: "crlf"
			}
		))
		.pipe(autoprefixer(
			{
				browsers: ['last 12 versions'],
				cascade: false
			}
		))
		.pipe(cssmin())
		.pipe(gulp.dest('css'))
		.pipe(sass({errLogToConsole: true}));


	gulp.src('sass/youzer/*.scss')
		.pipe(plumber())
		.pipe(sass(
			{
				linefeed: "crlf"
			}
		))
		.pipe(autoprefixer(
			{
				browsers: ['last 6 versions'],
				cascade: false
			}
		))
		.pipe(cssmin())
		.pipe(gulp.dest('css/youzer'))
		.pipe(sass({errLogToConsole: true}));

	gulp.src('Bootstrap/scss/*.scss')
		.pipe(plumber())
		.pipe(sass(
			{
				linefeed: "crlf"
			}
		))
		.pipe(autoprefixer(
			{
				browsers: ['last 6 versions'],
				cascade: false
			}
		))
		.pipe(cssmin())
		.pipe(gulp.dest('Bootstrap/dist/css'))
		.pipe(sass({errLogToConsole: true}));

});


/*/!*===========Watch==============*!/*/


gulp.task('watch', ['sass'], function (){

	gulp.watch('sass/**/*.scss', ['sass']);
	gulp.watch('Bootstrap/scss/*.scss', ['sass']);
	// others
});


/*/!*=============Join tasks==============*!/*/


gulp.task('default', function(callback) {
	runSequence(['sass', 'watch'],
		callback
	)
});
