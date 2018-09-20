var gulp   = require('gulp'),
    argv   = require('yargs').argv,
    config = require(process.cwd() + '/gulp-config.js'),
    uglify = require('gulp-uglify'),
    concat = require('gulp-concat');

gulp.task('combine-and-compress-vendor-and-main-js-files-into-all-file', function(build, cb) {
    var target = argv.target || 'site';

    gulp.src([config.builds[target].scripts.src + 'vendor.js', config.builds[target].scripts.src + 'main.js'])
        .pipe(concat('all.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.builds[target].scripts.dist));

    return gulp.src([config.builds[target].scripts.src + '*.js',
        '!' + config.builds[target].scripts.src + 'vendor.js',
        '!' + config.builds[target].scripts.src + 'main.js',
        '!' + config.builds[target].scripts.src + '*.map'])
        .pipe(uglify())
        .pipe(gulp.dest(config.builds[target].scripts.dist));

    cb(err);
});
