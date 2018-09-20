var gulp        = require('gulp'),
    argv        = require('yargs').argv,
    config      = require(process.cwd() + '/gulp-config.js'),
    browserSync = require('browser-sync').create();


gulp.task('watch-with-browser-sync', ['watch'], function() {
    var target = argv.target || 'site';

    browserSync.init({
        open: false,
        host: config.url,
        proxy: config.url
    });

    // Watch any files in root, reload on change
    gulp.watch([config.paths.root + '/**/*.{php,css,html,js,vue,svg}']).on('change', browserSync.reload);
});
