import Vue from 'vue'
import AsyncComputed from 'vue-async-computed'

Vue.component('workflow-simulation', require('./components/WorkflowSimulation.vue'));

Vue.use(AsyncComputed);

const app = new Vue({
    el: '#container'
});