import Vue from 'vue'

Vue.component('workflow-simulation', require('./components/WorkflowSimulation.vue'));

const app = new Vue({
    el: '.container'
});