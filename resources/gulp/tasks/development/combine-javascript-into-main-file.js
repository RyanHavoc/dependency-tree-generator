var gulp       = require('gulp'),
    argv       = require('yargs').argv,
    config     = require(process.cwd() + '/gulp-config.js'),
    concat     = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps');


gulp.task('combine-javascript-into-main-file', function(cb) {
    var target = argv.target || 'site';

    return gulp.src([
        config.builds[target].scripts.raw + '*/*.js',
        config.builds[target].scripts.raw + '*/*/*.js',
        config.builds[target].scripts.raw + '*.js',
    ])
    .pipe(sourcemaps.init())
    .pipe(concat('main.js'))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(config.builds[target].scripts.src))
});
