var gulp = require('gulp'),
    argv = require('yargs').argv,
    config = require(process.cwd() + '/gulp-config.js'),
    del = require('del');


gulp.task('reset', function(cb)
{
    var target = argv.target || 'site';

    del([
        '!.*',
        config.paths.css_dest + '/*',
        config.paths.scripts_dest + '/*',
        config.paths.assets_dest + '/rev-manifest.json',
        config.paths.icons_dest + '/*',
        '!' + config.paths.scripts_dest + '/lib'
    ], cb);
});
