var gulp = require('gulp'),
    argv = require('yargs').argv,
    config = require(process.cwd() + '/gulp-config.js'),
    del = require('del');


gulp.task('remove-development-files', function(cb) {
    return del([config.paths.css_dest + '/src',
                config.paths.scripts_dest + '/src'], cb);
});
