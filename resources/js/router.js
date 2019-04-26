
import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {
            path: '/', 
            redirect: '/login'
        }, 
        {
            name: 'login', 
            path: '/login', 
            component: require('./components/HomePage/Login.vue')
        }, 
        {
            name: 'dashboard', 
            path: '/dashboard', 
            component: require('./components/StreamerPage/Dashboard.vue')
        }
    ], 
});

export default router;