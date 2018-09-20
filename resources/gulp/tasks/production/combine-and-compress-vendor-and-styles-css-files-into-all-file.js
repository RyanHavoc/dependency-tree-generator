var gulp = require('gulp'),
    argv = require('yargs').argv,
    config = require(process.cwd() + '/gulp-config.js'),
    concat = require('gulp-concat'),
    minifycss = require('gulp-clean-css');

gulp.task('combine-and-compress-vendor-and-styles-css-files-into-all-file', function(build, cb) {
    var target = argv.target || 'site';

    return gulp.src([
        config.builds[target].styles.src + 'vendor.css',
        config.builds[target].styles.src + 'styles.css'
    ])
        .pipe(concat('all.css'))
        .pipe(minifycss())
        .pipe(gulp.dest(config.builds[target].styles.dist));

    cb(err);
});
