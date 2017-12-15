var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var watch = require('gulp-watch');
var gutil = require('gulp-util');

gulp.task('copy', function () {
	gulp.src([
		'bower_components/fullcalendar/dist/fullcalendar.min.css'
	])
		.pipe(gulp.dest('css'));
	gulp.src([
		'bower_components/fullcalendar/dist/fullcalendar.min.js',
		'bower_components/moment/min/moment-with-locales.min.js'
	])
		.pipe(gulp.dest('js'));
});

gulp.task('minify-css', function () {
	return gulp.src(['css/*.css', '!css/*.min.css'])
		.pipe(cleanCSS({compatibility: 'ie8'}))
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest('css'));
});

gulp.task('minify-js', function () {
	return gulp.src(['js/*.js', '!js/*.min.js'])
		.pipe(uglify())
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(gulp.dest('js'));
});

gulp.task('default', ['copy', 'minify-css', 'minify-js'], function () {
	gulp.watch(['css/*.css'], ['minify-css']);
	gulp.watch(['js/*.js'], ['minify-js']);
	gutil.log(gutil.colors.green('Build complete! Watching files ...'));
});