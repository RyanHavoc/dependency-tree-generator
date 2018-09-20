var paths = {
    root:          'public',
    assets_dest:   'public/assets',
    css_dest:      'public/assets/css',
    scripts_dest:  'public/assets/js',
    library:       'resources/library',
    project:       'resources/project',
    vendor:        'node_modules'
};

var builds = {
    site: {
        styles: {
            raw:  paths.project + '/site/',
            src:  paths.css_dest + '/src/site/',
            dist: paths.css_dest + '/dist/site/'
        },

        scripts: {
            raw:  paths.project + '/site/js/',
            src:  paths.scripts_dest + '/src/site/',
            dist: paths.scripts_dest + '/dist/site/'
        },

        templates: {
            raw: 'resources/views/'
        }
    }
};

var vendor = {
    site: {
        styles: [],
        scripts: {
            head: [],
            foot: []
        }
    },
    astrum: {
        styles: [],
        scripts: {
            head: [],
            foot: []
        }
    }
};

module.exports = {
    url: 'snyk-exercise.loc',
    paths: paths,
    builds: builds,
    vendor: vendor
};
