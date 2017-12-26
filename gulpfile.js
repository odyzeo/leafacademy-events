var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var watch = require('gulp-watch');
var gutil = require('gulp-util');
var sass = require('gulp-sass');
var pump = require('pump');
var dateFormat = require('dateformat');
var zip = require('gulp-zip');
var del = require('del');

var paths = {
	css: ['css/*.css', '!css/*.min.css'],
	js: ['js/*.js', '!js/*.min.js'],
	sass: ['scss/*.scss']
};

gulp.task('copy', function(cb) {

	pump([
		gulp.src([
			'bower_components/fullcalendar/dist/fullcalendar.min.css',
			'bower_components/flexboxgrid/dist/flexboxgrid.min.css'
		]),
		gulp.dest('css')
	]);

	pump([
		gulp.src([
			'bower_components/fullcalendar/dist/fullcalendar.min.js',
			'bower_components/moment/min/moment-with-locales.min.js',
			'bower_components/gasparesganga-jquery-loading-overlay/src/loadingoverlay.min.js',
			'bower_components/gasparesganga-jquery-loading-overlay/extras/loadingoverlay_progress/loadingoverlay_progress.min.js',
			'node_modules/tippy.js/dist/tippy.all.min.js',
			'bower_components/handlebars/handlebars.min.js',
			'bower_components/handlebars/handlebars.runtime.min.js'
		]),
		gulp.dest('js')
	]);

	pump([
		gulp.src([
			'bower_components/gasparesganga-jquery-loading-overlay/src/loading.gif'
		]),
		gulp.dest('images')
	], cb);

});

gulp.task('minify-css', function(cb) {

	pump([
		gulp.src(paths.css),
		cleanCSS({compatibility: 'ie8'}),
		rename({
			suffix: '.min'
		}),
		gulp.dest('css')
	], cb);

});

gulp.task('minify-js', function(cb) {

	pump([
		gulp.src(paths.js),
		uglify(),
		rename({
			suffix: '.min'
		}),
		gulp.dest('js')
	], cb);

});

gulp.task('sass', function(cb) {

	pump([
		gulp.src('./scss/*.scss'),
		sass().on('error', sass.logError),
		gulp.dest('./css')
	], cb);

});

gulp.task('build', ['copy', 'sass', 'minify-css', 'minify-js'], function(cb) {

	pump([
		gulp.src([
			'**/*',
			'!node_modules',
			'!node_modules/**',
			'!bower_components',
			'!bower_components/**',
			'!scss',
			'!scss**',
			'!dist',
			'!dist/**',
			'!packaged',
			'!packaged/**',
			'!bower.json',
			'!gulpfile.js',
			'!package.json',
			'!package-lock.json',
			'!codesniffer.ruleset.xml',
			'!*.md',
			'!**/*.scss'
		]),
		gulp.dest('dist')
	], cb);

});

gulp.task('watch', ['build'], function() {

	gulp.watch(paths.css, ['minify-css']);
	gulp.watch(paths.js, ['minify-js']);
	gulp.watch(paths.sass, ['sass', 'minify-css']);

	gutil.log(gutil.colors.green('Watching files ...'));

});

gulp.task('package', ['build'], function(cb) {

	var fs = require('fs');
	var time = dateFormat(new Date(), "yyyy-mm-dd_HH-MM");
	var pkg = JSON.parse(fs.readFileSync('./package.json'));
	var filename = pkg.name + '-' + pkg.version + '-' + time + '.zip';

	pump([
		gulp.src([
			'./dist/**/*'
		]),
		zip(filename),
		gulp.dest('packaged')
	], cb);

});

gulp.task('clean', function() {

	return del([
		'dist',
		'packaged'
	]);

});

gulp.task('default', ['build']);