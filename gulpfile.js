var gulp = require('gulp'),
  rename = require('gulp-rename'),
  cssmin = require('gulp-clean-css'),
  less = require('gulp-less'),
  shell = require('gulp-shell'),
  use_sourcemaps = false
;

var basePath = './web/profiles/chlovet/themes';

// LESS compilation
gulp.task('less-vetbase', function () {
  var pipe = gulp.src(basePath + '/vetbase/less/style.less');
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.init());
  }
  pipe = pipe
    .pipe(less())
    .pipe(cssmin())
    .pipe(rename({suffix: '.min'}));
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.write(basePath + '/base/maps'));
  }
  return pipe
    .pipe(gulp.dest(basePath + '/vetbase/dist/'))
    .on('error', errorHandler);
});

gulp.task('less-vetadmin', function () {
  var pipe = gulp.src(basePath + '/vetadmin/less/style.less');
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.init());
  }
  pipe = pipe
    .pipe(less())
    .pipe(cssmin())
    .pipe(rename({suffix: '.min'}));
  if (use_sourcemaps) {
    pipe = pipe.pipe(sourcemaps.write(basePath + '/vetadmin/maps'));
  }
  return pipe
    .pipe(gulp.dest(basePath + '/vetadmin/dist/'))
    .on('error', errorHandler);
});

gulp.task('drush-cc', function () { shell(['./bin/drush cc css-js']); });
gulp.task('less', ['less-vetadmin', 'less-vetbase', 'drush-cc']);
gulp.task('default', ['less']);
gulp.task('watch', function () { gulp.watch('./less/**/*', ['less']); });

// Handle the error
function errorHandler(error) {
  console.log(error.toString());
  this.emit('end');
}
