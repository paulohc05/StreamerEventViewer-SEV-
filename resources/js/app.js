
import Vue from 'vue';
import AppVue from './App.vue';
import BootstrapVue from 'bootstrap-vue';
import router from './router';

Vue.use(BootstrapVue);
Vue.use(router);

const app = new Vue({
    el: '#app', 
    router, 
    render: h => h(AppVue)
});