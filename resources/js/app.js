
import Vue from 'vue';
import AppVue from './App.vue';
import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);

const app = new Vue({
    el: '#app', 
    render: h => h(AppVue)
});