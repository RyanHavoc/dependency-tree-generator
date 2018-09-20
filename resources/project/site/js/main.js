const Vue = require('vue/dist/vue');

// Define site apps
const apps = {
    'dependencyTree': require('./apps/dependencyTree.js')
};

// Loop each item and bind to Vue
Object.keys(apps).map(key => new Vue(apps[key]));
