var gulp       = require('gulp'),
    argv       = require('yargs').argv,
    config     = require(process.cwd() + '/gulp-config.js'),
    concat     = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps');

gulp.task('combine-javascript-dependencies-into-vendor-file', function(cb)
{
    var target = argv.target || 'site';

    if(config.vendor[target].scripts.foot)
    {
        return gulp.src(config.vendor[target].scripts.foot)
                   .pipe(sourcemaps.init())
                   .pipe(concat('vendor.js'))
                   .pipe(sourcemaps.write('.'))
                   .pipe(gulp.dest(config.builds[target].scripts.src))
    }
});
