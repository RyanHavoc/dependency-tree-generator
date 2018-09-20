var gulp       = require('gulp'),
    argv       = require('yargs').argv,
    config     = require(process.cwd() + '/gulp-config.js'),
    concat     = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps');


gulp.task('combine-css-dependencies-into-vendor-file', function(cb)
{
    var target = argv.target || 'site';

    return gulp.src(config.vendor[target].styles)
               .pipe(sourcemaps.init())
               .pipe(concat('vendor.css'))
               .pipe(sourcemaps.write('.'))
               .pipe(gulp.dest(config.builds[target].styles.src));
});
