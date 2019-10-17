require('./bootstrap');

window.Vue = require('vue');

Vue.component('invoice-positions', require('./components/InvoicePositions.vue').default);
Vue.component('invoice-position', require('./components/InvoicePosition.vue').default);
Vue.component('prescribing-form', require('./components/PrescribingForm').default);
Vue.component('add-person-modal', require('./components/AddPersonModal.vue').default);
Vue.component('student-list-table', require('./components/StudentListTable.vue').default);
Vue.component('student', require('./components/Student.vue').default);
Vue.component('position-tab', require('./components/PositionTab.vue').default);
Vue.component('validation-errors', require('./components/ValidationErrors.vue').default);

const app = new Vue({
    el: '#app',
    data: {
    	
    },
    methods: {
        
    },
    mounted() {
        console.log('app.js mounted');
    }
});