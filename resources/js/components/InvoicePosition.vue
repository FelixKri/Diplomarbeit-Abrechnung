<template>
    <div class="tab-pane fade show" :id="'nav-' + position.name" role="tabpanel" :aria-labelledby="'nav-' + position.name + '-tab'">
                <h2>ID: {{position.id}} | NAME: {{position.name}}</h2>
                
                <div class="row">

                    <div class="col-sm">
                        <div class="form-group">
                            <label for="amount">Betrag</label>
                            <input type="number" class="form-control" placeholder="Betrag">
                        </div>
                        <div class="form-group">
                            <label for="billnumber">BelegNr</label>
                            <input type="number" class="form-control" placeholder="Belegnummer">
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="annotation">Anmerkungen</label>
                            <textarea class="form-control" id="annotation" rows="5"></textarea>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary btn-sm" data-toggle="modal" :data-target="'#addUser_'+position.id" type=button>Person(n) hinzufügen</button>

                <add-person-modal v-on:addstudents="addStudents" :id="position.id"></add-person-modal>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">X</th>
                            <th scope="col">ID</th>
                            <th scope="col">Nachname</th>
                            <th scope="col">Vorname</th>
                            <th scope="col">Klasse</th>
                            <th scope="col">Betrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        <student-invoice v-bind:key="student.id" v-for="student in position.students" :student="student"></student-invoice>
                    </tbody>
                </table>
            </div>
</template>

<script>
    export default {
        created: function() {
            console.log('Component created: InvoicePosition');

        },
        data: function () {
            return {
                studentsDom: [],
                studentsLoaded: [],
                studentsLoadedLength: 0,
                errors: {},
                groups: [],
                groupLength: 0
            }
        },
        props: [
            "position",
        ],
        methods: {
            addStudents: function(studentsDom)
            {
                this.position.students = studentsDom;
                
                console.log(this.position.students);
            }
        }
    }
</script>
