const gulp = require('gulp');
const sass = require('gulp-sass');
const uglify = require('gulp-clean-css');
const autoPrefixer = require('gulp-autoprefixer');

/*

  --- Top Level Functions ---

  gulp.task - Define tasks
  gulp.src - Point to files to user
  gulp.dest - Points to folder to output
  gulp.watch - Watch files and folders for changes

*/


//
// Sass
//

gulp.task('sass', function(){
  gulp.src('./hestia-child/sass/style.scss')
    .pipe(sass().on('error', sass.logError))
    .pipe(autoPrefixer())
    // .pipe(uglify())
    .pipe(gulp.dest('./hestia-child/'));
});


//
// Default
//

gulp.task('default', ['sass']);

gulp.task('watch', function(){
  gulp.watch('./hestia-child/sass/**/*.scss', ['sass']);
  gulp.watch('./hestia-child/sass/style.scss', ['sass']);
})
