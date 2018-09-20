var gulp = require('gulp'),
    argv = require('yargs').argv,
    babelify = require('babelify'),
    config = require(process.cwd() + '/gulp-config.js'),
    source = require('vinyl-source-stream'),
    stringify = require('stringify'),
    browserify = require('browserify'),
    vueify = require('vueify');


gulp.task('compile-js-with-browserify', function() {
    var target = argv.target || 'site';

    return browserify({
            entries: config.builds[target].scripts.raw + 'main.js',
            debug: !(process.env.NODE_ENV === 'production')
        })
        .transform(babelify.configure({
            presets: ['env']
        }))
        .transform(vueify)
        .transform(stringify, {
            appliesTo: { includeExtensions: ['.html'] }
        })
        .bundle()
        .pipe(source('main.js'))
        .pipe(gulp.dest(config.builds[target].scripts.src));
});
