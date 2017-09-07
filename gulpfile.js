var gulp = require('gulp'),
    configLocal = require('./gulp-config.json'),
    merge = require('merge'),
    sass = require('gulp-sass'),
    bless = require('gulp-bless'),
    rename = require('gulp-rename'),
    scsslint = require('gulp-scss-lint'),
    autoprefixer = require('gulp-autoprefixer'),
    cleanCSS = require('gulp-clean-css'),
    jshint = require('gulp-jshint'),
    jshintStylish = require('jshint-stylish'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat'),
    browserSync = require('browser-sync').create();

var configDefault = {
    scssPath: './src/scss',
    cssPath: './static/css',
    jsPath: './src/js',
    jsOutPath: './static/js',
  },
  config = merge(configDefault, configLocal);


// Lint all scss files
gulp.task('scss-lint', function() {
  gulp.src(config.scssPath + '/*.scss')
    .pipe(scsslint());
});

// Compile + bless primary scss files
gulp.task('css-main', function() {
  gulp.src(config.scssPath + '/ucf-resource-search.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9'],
      cascade: false
    }))
    .pipe(cleanCSS({compatibility: 'ie9'}))
    .pipe(rename('ucf-resource-search.min.css'))
    .pipe(bless())
    .pipe(gulp.dest(config.cssPath))
    .pipe(browserSync.stream());
});

// All css-related tasks
gulp.task('css', ['scss-lint', 'css-main']);

gulp.task('js-hint', function() {
  gulp.src(config.jsPath + '/*.js')
    .pipe(jshint())
    .pipe(jshint.reporter('jshint-stylish'))
    .pipe(jshint.reporter('fail'));
});

gulp.task('js-min', function() {
  var minified = [
    config.jsPath + '/ucf-resource-search.js'
  ];

  gulp.src(minified)
    .pipe(concat('ucf-resource-search.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest(config.jsOutPath));
});

gulp.task('js', ['js-hint', 'js-min']);

// Rerun tasks when files change
gulp.task('watch', function() {
  if (config.sync) {
    browserSync.init({
        proxy: {
          target: config.target
        }
    });
  }

  gulp.watch(config.scssPath + '/**/*.scss', ['css']);
  gulp.watch(config.jsPath + '/*.js', ['js']).on('change', browserSync.reload);
  gulp.watch('./**/*.php').on('change', browserSync.reload);
});

// Default task
gulp.task('default', ['css', 'js']);
