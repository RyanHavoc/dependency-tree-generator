var gulp = require('gulp'),
    argv = require('yargs').argv,
    config = require(process.cwd() + '/gulp-config.js'),
    globImporter = require('sass-glob-importer'),
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer');

gulp.task('compile-sass-into-styles-file', function(cb)
{
    var target = argv.target || 'site';
    
    return gulp.src(config.builds[target].styles.raw + 'styles.scss')
        .pipe(sourcemaps.init())
        .pipe(sass({
            importer: globImporter()
        })
        .on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: 'last 2 versions',
            cascade: false
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest(config.builds[target].styles.src));
});
