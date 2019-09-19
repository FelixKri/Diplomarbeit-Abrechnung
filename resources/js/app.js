require('./bootstrap');

window.Vue = require('vue');

Vue.component('add-person-modal', require('./components/AddPersonModal.vue').default);
Vue.component('student-list-table', require('./components/StudentListTable.vue').default);
Vue.component('student', require('./components/Student.vue').default);
Vue.component('invoice-positions', require('./components/InvoicePositions.vue').default);
Vue.component('invoice-position', require('./components/InvoicePosition.vue').default);
Vue.component('position-tab', require('./components/PositionTab.vue').default);
Vue.component('prescribing-form', require('./components/PrescribingForm').default);
Vue.component('validation-errors', require('./components/ValidationErrors.vue').default);

var data = {
    
};

//Seems really stupid to include groupLength
//But objects don't have a .length property
//So to not count this everytime we use it we just include it

const app = new Vue({
    el: '#app',
    data: data,
    methods: {
        
    },
    mounted() {
        console.log('app.js mounted');
    }
});