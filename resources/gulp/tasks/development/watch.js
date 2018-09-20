var gulp        = require('gulp'),
    argv        = require('yargs').argv,
    config      = require(process.cwd() + '/gulp-config.js'),
    runSequence = require('run-sequence');


gulp.task('watch', function() {
    var target = argv.target || 'site';

    // Watch .scss files
    gulp.watch([config.builds[target].styles.raw + '/**/*.scss', config.paths.library + '/**/*.scss'], function() {
        runSequence('compile-sass-into-styles-file');
    });

    // Watch .js files
    gulp.watch([config.builds[target].scripts.raw + '**'], function() {
        runSequence('compile-js-with-browserify');
    });
});
