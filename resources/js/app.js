require('./bootstrap');

window.Vue = require('vue');

Vue.component('add-person-modal', require('./components/AddPersonModal.vue').default);
Vue.component('student-list-table', require('./components/StudentListTable.vue').default);
Vue.component('student', require('./components/Student.vue').default);
Vue.component('invoice-positions', require('./components/InvoicePositions.vue').default);
Vue.component('invoice-position', require('./components/InvoicePosition.vue').default);
Vue.component('position-tab', require('./components/PositionTab.vue').default);
Vue.component('prescribing-form', require('./components/PrescribingForm').default);

 var data = {
    students: [],
    studentsDom: [],
    studentsLoaded: [],
    studentsLoadedLength: 0,
    groups: [],
    groupLength: 0
};

//Seems really stupid to include groupLength
//But objects don't have a .length property
//So to not count this everytime we use it we just include it

$.ajax(
{
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: "POST",
    url: '/getAllGroups',
    dataType: 'json',
    data: {
    },

    success: function (response) {
        data.groups = response;

        var count = 0;

        for(var thing in data.groups)
        {
            count++;
        }

        data.groupLength = count;
    }
});

const app = new Vue({
    el: '#app',
    data: data,
    methods: {
        addStudents: function()
        {
            console.log("Adding students:");
            console.log(this.studentsDom);

            //Todo check for duplicates
            //Add to students
            this.students = this.studentsDom;
        }
    },
    mounted() {
        console.log('app.js mounted');
    }
});