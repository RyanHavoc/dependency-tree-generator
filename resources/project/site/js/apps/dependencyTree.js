const Vue = require('vue');
const semverSort = require('semver-sort');

module.exports = {
    el: '[data-app="dependencyTree"]',

    components: {
        'dependency-tree': require('../components/DependencyTree.vue')
    },

    data: {
        packageName: null,
        packageVersion: null,
        packageVersions: null,
        dependencyTree: null,
        loading: false,
        notFound: false
    },

    mounted() {

    },

    methods: {
        submit(e) {
            e.preventDefault();

            this.packageVersions = null;
            this.notFound = false;
            this.loading = true;
            this.fetchPackage(this.packageName);
        },
        changePackage(name) {
            this.reset();

            this.packageName = name;
            this.loading = true;
            this.fetchPackage(this.packageName);
        },
        changeVersion(e) {
            this.loading = true;
            this.fetchDependencyTree(this.packageName, this.packageVersion);
        },
        fetchPackage(name) {
            const self = this;

            fetch(`/api/package/${name}`)
                .then(data => data.json())
                .then(data => {
                    if (data.error) {
                        this.reset();

                        self.notFound = true;
                    } else {
                        self.fetchDependencyTree(name, data['dist-tags'].latest);
                        self.packageVersion = data['dist-tags'].latest;
                        self.packageVersions = semverSort.desc(Object.keys(data.versions));
                    }
                });
        },
        fetchDependencyTree(name, version) {
            const self = this;

            fetch(`/api/dependency-tree/${name}/${version}`)
                .then(data => data.json())
                .then(data => {
                    self.dependencyTree = data;
                    self.loading = false;
                });
        },
        reset() {
            this.packageName = null;
            this.packageVersion = null;
            this.packageVersions = null;
            this.dependencyTree = null;
            this.loading = false;
            this.notFound = false;
        }
    }
};