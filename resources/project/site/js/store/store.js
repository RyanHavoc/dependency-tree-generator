import Vue from 'vue/dist/vue.js';
import VueX from 'vuex';

// Import actions, getters and mutations from their respective directories 
import actions from './actions';
import getters from './getters';
import mutations from './mutations';

Vue.use(VueX);

// The core data structure of the store
const state = {
};

export default new VueX.Store({ 
    state,
    mutations,
    actions,
    getters
});
