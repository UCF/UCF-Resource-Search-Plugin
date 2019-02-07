const autoprefixer = require('gulp-autoprefixer');
const babel        = require('gulp-babel');
const browserSync  = require('browser-sync').create();
const cleanCSS     = require('gulp-clean-css');
const eslint       = require('gulp-eslint');
const fs           = require('fs');
const gulp         = require('gulp');
const isFixed      = require('gulp-eslint-if-fixed');
const merge        = require('merge');
const readme       = require('gulp-readme-to-markdown');
const rename       = require('gulp-rename');
const sass         = require('gulp-sass');
const sassLint     = require('gulp-sass-lint');
const uglify       = require('gulp-uglify');

let config = {
  src: {
    scssPath: './src/scss',
    jsPath: './src/js',
    fontPath: './src/fonts'
  },
  static: {
    cssPath: './static/css',
    jsPath: './static/js'
  },
  sync: false,
  syncOptions: {},
};

if (fs.existsSync('./gulp-config.json')) {
  const overrides = JSON.parse(fs.readFileSync('./gulp-config.json'));
  config = merge(config, overrides);
}


//
// CSS
//

// Base scss linting function
// NOTE: see global linter rules and excluded files in .sass-lint.yml
function lintSCSS(src) {
  return gulp.src(src)
    .pipe(sassLint())
    .pipe(sassLint.format())
    .pipe(sassLint.failOnError());
}

// Compile scss files
function buildCSS(src, filename, dest) {
  dest = dest || config.static.cssPath;

  return gulp.src(src)
    .pipe(sass({
      includePaths: [config.src.scssPath]
    })
    .on('error', sass.logError))
    .pipe(cleanCSS())
    .pipe(autoprefixer({
      // Supported browsers added in package.json ("browserslist")
      cascade: false
    }))
    .pipe(rename(filename))
    .pipe(gulp.dest(dest));
}

// Lint scss files. Do not perform linting on vendor scss files.
gulp.task('scss-lint', () => {
  return lintSCSS(`${config.src.scssPath}/**/*.scss`);
});

// Compile framework scss files
gulp.task('scss-build', () => {
  return buildCSS(config.src.scssPath + '/ucf-resource-search.scss', 'ucf-resource-search.min.css', config.static.cssPath);
});

// All css-related tasks
gulp.task('css', gulp.series('scss-lint', 'scss-build'));

//
// JavaScript
//

// Base JS linting function (with eslint). Fixes problems in-place.
// NOTE: see global linter rules in .eslintrc.json and excluded files
// in .eslintignore
function lintJS(src, dest) {
  dest = dest || config.src.jsPath;

  return gulp.src(src)
    .pipe(eslint({
      fix: true
    }))
    .pipe(eslint.format())
    .pipe(isFixed(dest));
}

// Concat and uglify js files through babel
function buildJS(src, filename, dest) {
  dest = dest || config.dist.jsPath;

  return gulp.src(src)
    .on('error', console.log) // eslint-disable-line no-console
    .pipe(babel())
    .pipe(uglify({
      output: {
        // try to preserve non-standard headers (e.g. from objectFitPolyfill)
        comments: /^(!|---)/
      }
    }))
    .pipe(rename(filename))
    .pipe(gulp.dest(dest));
}


// Run eslint on js files in src.jsPath. Do not perform linting
// on vendor js files. See .eslintignore for globally ignored files.
gulp.task('es-lint', () => {
  return lintJS(`${config.src.jsPath}/*.js`, config.src.jsPath);
});

// Concat and uglify framework js files through babel
gulp.task('js-build', () => {
  return buildJS(`${config.src.jsPath}/ucf-resource-search.js`, 'ucf-resource-search.min.js', config.static.jsPath);
});

// All js-related tasks
gulp.task('js', gulp.series('es-lint', 'js-build'));


// BrowserSync reload function
function serverReload(done) {
  if (config.sync) {
    browserSync.reload();
  }
  done();
}

// BrowserSync serve function
function serverServe(done) {
  if (config.sync) {
    browserSync.init(config.syncOptions);
  }
  done();
}

//
// Readme
//

// Create a Github-flavored markdown file from the plugin readme.txt
gulp.task('readme', function() {
  return gulp.src(['readme.txt'])
    .pipe(readme({
      details: false,
      screenshot_ext: [],
    }))
    .pipe(gulp.dest('.'));
});


//
// Rerun tasks when files change.
//

gulp.task('watch', (done) => {
  serverServe(done);

  gulp.watch(`${config.src.scssPath}/**/*.scss`, gulp.series('css', serverReload));
  gulp.watch(`${config.src.jsPath}/**/*.js`, gulp.series('js', serverReload));
  gulp.watch(`./**/*.php`, serverReload);
});

//
// Default task
//

gulp.task('default', gulp.series('css', 'js', 'readme'));
