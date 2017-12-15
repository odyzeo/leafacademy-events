var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var watch = require('gulp-watch');
var gutil = require('gulp-util');
var sass = require('gulp-sass');

gulp.task('copy', function() {
	gulp.src([
		'bower_components/fullcalendar/dist/fullcalendar.min.css',
		'bower_components/flexboxgrid/dist/flexboxgrid.min.css'
	])
		.pipe(gulp.dest('css'));

	gulp.src([
		'bower_components/fullcalendar/dist/fullcalendar.min.js',
		'bower_components/moment/min/moment-with-locales.min.js',
		'bower_components/gasparesganga-jquery-loading-overlay/src/loadingoverlay.min.js',
		'bower_components/gasparesganga-jquery-loading-overlay/extras/loadingoverlay_progress/loadingoverlay_progress.min.js'
	])
		.pipe(gulp.dest('js'));

	gulp.src([
		'bower_components/gasparesganga-jquery-loading-overlay/src/loading.gif'
	])
		.pipe(gulp.dest('images'));
});

gulp.task('minify-css', function() {
	return gulp.src(['css/*.css', '!css/*.min.css'])
		.pipe(cleanCSS({compatibility: 'ie8'}))
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest('css'));
});

gulp.task('minify-js', function() {
	return gulp.src(['js/*.js', '!js/*.min.js'])
		.pipe(uglify())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest('js'));
});

gulp.task('sass', function() {
	return gulp.src('./scss/*.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(gulp.dest('./css'));
});

gulp.task('default', ['copy', 'sass', 'minify-css', 'minify-js'], function() {
	gulp.watch(['css/*.css'], ['minify-css']);
	gulp.watch(['js/*.js'], ['minify-js']);
	gulp.watch(['scss/*.scss'], ['sass', 'minify-css']);
	gutil.log(gutil.colors.green('Build complete! Watching files ...'));
});