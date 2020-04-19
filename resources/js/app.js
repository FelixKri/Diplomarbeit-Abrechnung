require('./bootstrap');

window.Vue = require('vue');

Vue.component('invoice-position', require('./components/InvoicePosition.vue').default);
Vue.component('prescribing-form', require('./components/PrescribingForm').default);
Vue.component('add-person-modal', require('./components/AddPersonModal.vue').default);
Vue.component('student-list-table', require('./components/StudentListTable.vue').default);
Vue.component('student', require('./components/Student.vue').default);
Vue.component('position-tab', require('./components/PositionTab.vue').default);
Vue.component('validation-errors', require('./components/ValidationErrors.vue').default);
Vue.component('invoice-form', require('./components/InvoiceForm.vue').default);
Vue.component('student-invoice', require('./components/StudentInvoice.vue').default);
Vue.component('prescribing-list', require('./components/PrescribingList.vue').default);
Vue.component('prescribing-detail', require('./components/PrescribingDetail.vue').default);
Vue.component('add-from-prescribing-modal', require('./components/AddPersonFromPrescribingModal.vue').default);
Vue.component('invoice-list', require('./components/InvoiceList.vue').default);
Vue.component('invoice-detail', require('./components/InvoiceDetail.vue').default);
Vue.component('invoice-detail-position', require('./components/InvoiceDetailPosition.vue').default);
Vue.component('student-invoice-detail', require('./components/StudentInvoiceDetail').default);
Vue.component('invoice-overview-position', require('./components/InvoiceOverviewPosition.vue').default);
Vue.component('student-overview-invoice', require('./components/StudentOverviewInvoice.vue').default);

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