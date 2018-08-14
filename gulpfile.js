'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass');
const rename = require("gulp-rename");
const cssmin = require('gulp-cssmin');
const gutil = require('gulp-util');
const autoprefixer = require('gulp-autoprefixer');


gulp.task('sass', function () {
    var files = './assets/sass/[^_]*.scss';

    return gulp.src(files)
        // create .css file
        .pipe(sass())
        .on('error', gutil.log)
        .pipe(rename({ extname: '.css' }))
        .pipe(gulp.dest('./assets/css'))

        // create .min.css
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(cssmin())
        .pipe(rename({extname: '.min.css'}))
        .pipe(gulp.dest("./assets/css"))
        .pipe(gulp.dest("./_site/assets/css"));
});

gulp.task('watch', function () {
    gulp.watch('./assets/sass/**/*.scss', gulp.series(['sass']));
});


gulp.task('default', gulp.series(['sass', 'watch']));
