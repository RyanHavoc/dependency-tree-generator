var gulp   = require('gulp'),
    argv   = require('yargs').argv,
    config = require(process.cwd() + '/gulp-config.js'),
    concat = require('gulp-concat');

gulp.task('combine-head-specific-javascript-into-head-file', function(cb)
{
    var target = argv.target || 'site';

    if(config.vendor[target].scripts.head)
    {
        return gulp.src(config.vendor[target].scripts.head)
                   .pipe(concat('head.js'))
                   .pipe(gulp.dest(config.builds[target].scripts.src))
    }
});
