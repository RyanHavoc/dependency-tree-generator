var gulp = require('gulp'),
    argv = require('yargs').argv,
    config = require(process.cwd() + '/gulp-config.js'),
    del = require('del');

gulp.task('remove-existing-css-and-js-revision-files', function(cb) {
    var target = argv.target || 'site';

    del([
        config.builds[target].styles.dist + '*-*.css',
        config.builds[target].scripts.dist + '*-*.js'
    ]);
});
