/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
Vue.component('add-class-modal', require('./components/AddClassModal.vue').default);
Vue.component('add-person-modal', require('./components/AddPersonModal.vue').default);
Vue.component('student-list-table', require('./components/StudentListTable.vue').default);
Vue.component('student', require('./components/Student.vue').default);
Vue.component('invoice-positions', require('./components/InvoicePositions.vue').default);
Vue.component('invoice-position', require('./components/InvoicePosition.vue').default);
Vue.component('position-tab', require('./components/PositionTab.vue').default);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

var data = {
    classes: [],
    students: [],
};

const app = new Vue({
    el: '#app',
    data: data,
    methods: {

    },
    mounted() {
        console.log('app.js mounted');
    }
});