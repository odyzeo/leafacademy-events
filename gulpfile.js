var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var watch = require('gulp-watch');
var gutil = require('gulp-util');
var sass = require('gulp-sass');
var pump = require('pump');

gulp.task('copy', function() {

	pump([
		gulp.src([
			'bower_components/fullcalendar/dist/fullcalendar.min.css',
			'bower_components/flexboxgrid/dist/flexboxgrid.min.css'
		]), (gulp.dest('css'))
	]);

	pump([
		gulp.src([
			'bower_components/fullcalendar/dist/fullcalendar.min.js',
			'bower_components/moment/min/moment-with-locales.min.js',
			'bower_components/gasparesganga-jquery-loading-overlay/src/loadingoverlay.min.js',
			'bower_components/gasparesganga-jquery-loading-overlay/extras/loadingoverlay_progress/loadingoverlay_progress.min.js',
			'node_modules/tippy.js/dist/tippy.all.min.js'
		]), (gulp.dest('js'))
	]);

	pump([
		gulp.src([
			'bower_components/gasparesganga-jquery-loading-overlay/src/loading.gif'
		])
		, (gulp.dest('images'))
	]);
});

gulp.task('minify-css', function() {
	return pump([
		gulp.src(['css/*.css', '!css/*.min.css'])
		, (cleanCSS({compatibility: 'ie8'}))
		, (rename({
			suffix: '.min'
		}))
		, (gulp.dest('css'))
	]);
});

gulp.task('minify-js', function() {
	return pump([gulp.src(['js/*.js', '!js/*.min.js'])
		, (uglify())
		, (rename({
			suffix: '.min'
		}))
		, (gulp.dest('js'))
	]);
});

gulp.task('sass', function() {
	return pump([gulp.src('./scss/*.scss')
		, (sass().on('error', sass.logError))
		, (gulp.dest('./css'))
	]);
});

gulp.task('default', ['copy', 'sass', 'minify-css', 'minify-js'], function() {
	gulp.watch(['css/*.css'], ['minify-css']);
	gulp.watch(['js/*.js'], ['minify-js']);
	gulp.watch(['scss/*.scss'], ['sass', 'minify-css']);
	gutil.log(gutil.colors.green('Build complete! Watching files ...'));
});