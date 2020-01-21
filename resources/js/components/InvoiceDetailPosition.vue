<template>
    <div
        class="tab-pane fade show"
        :id="'nav-' + position.name"
        role="tabpanel"
        :aria-labelledby="'nav-' + position.name + '-tab'"
    >
        <h2>{{ position.name }}</h2>

        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="amount">Betrag</label>
                    <input
                        type="number"
                        class="form-control"
                        placeholder="Betrag"
                        v-model="position.amount"
                    />
                </div>
                <input type="checkbox" v-model="position.paidByTeacher"><span>Von Lehrpersonal bezhalt</span>
                <div class="form-group">
                    <label for="billnumber">BelegNr</label>
                    <input
                        type="number"
                        class="form-control"
                        placeholder="Belegnummer"
                        v-model="position.document_number"
                    />
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="annotation">Anmerkungen</label>
                    <textarea
                        class="form-control"
                        id="annotation"
                        rows="5"
                        v-model="position.annotation"
                    ></textarea>
                </div>
            </div>
        </div>
        <table>
            <tr>
                <td>
                    <input
                        type="number"
                        id="number"
                        placeholder="Betrag"
                        class="form-control"
                        v-model="amount_st"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="splitEveryone()"
                        type="button"
                        value="Auf alle Aufteilen"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="splitSelected()"
                        type="button"
                        value="Auf ausgewählte Aufteilen"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="assignEveryone()"
                        type="button"
                        value="Betrag allen zuweisen"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="assignSelected()"
                        type="button"
                        value="Betrag ausgewählten zuweisen "
                    />
                </td>
            </tr>
            <tr>
                <td>
                    <input
                        type="radio"
                        id="type"
                        name="type"
                        value="overwrite"
                        checked
                        v-model="type"
                    />
                    <label for="type">Überschreiben</label>
                </td>
                <td>
                    <input
                        type="radio"
                        id="type"
                        name="type"
                        value="add"
                        v-model="type"
                    />
                    <label for="type">Hinzuaddieren</label>
                </td>
            </tr>
        </table>

        <button
            class="btn btn-primary btn-sm"
            data-toggle="modal"
            :data-target="'#addUser_' + position.id"
            type="button"
        >
            Person(en) hinzufügen
        </button>
        <button
            class="btn btn-primary btn-sm"
            data-toggle="modal"
            :data-target="'#getFromPrescribing_' + position.id"
            type="button"
        >
            Personen aus Vorschreibung übernehmen
        </button>

        <add-person-modal
            v-on:addstudents="addStudents"
            :id="position.id"
        ></add-person-modal>
        <add-from-prescribing-modal
            v-on:addstudents="addStudents"
            :id="position.id">
        </add-from-prescribing-modal>
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
                <student-invoice-detail
                    v-bind:key="student.id"
                    v-for="student in position.user_has_invoice_position"
                    :student="student"
                    v-on:removeStudent="removeStudent($event)"
                ></student-invoice-detail>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    created: function() {
        console.log("Component created: InvoicePosition");
    },
    data: function() {
        return {
            // amount: 0,
            // annotation: "",
            // belegNr: 0,
            errors: {},
            groups: [],
            groupLength: 0,
            amount_st: 0,
            type: false
        };
    },
    props: ["position"],
    methods: {
        getStudents: function()
        {
            //Make array of real students
            var studentsArray = [];

            this.position.user_has_invoice_position.forEach(function(student){
                studentsArray.push(student["user"]);
            });

            return studentsArray;
        },
        addStudents: function(studentsDom) {

            console.log("Before addStudents: ");
            console.log(this.position.user_has_invoice_position);

            //StudentsDom is pure students, so make them a viable position
            var positions = [];

            var today = new Date();
            var year = today.getFullYear();
            var dd = today.getDate();
            var mm = today.getMonth() + 1;
            var hh = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();

            var currentDate = year + "-" + mm + "-" + dd + " " + hh + ":" + m + ":" + s;

            var that = this;

            studentsDom.forEach(function(student){
                    var position = {
                    id:0,
                    invoice_position_id: that.position.id,
                    comment:null,
                    amount:0,
                    user:student,
                    created_at:currentDate,
                    updated_at:currentDate,
                    user_id: student.id
                    };

                    console.log("Position");
                    console.log(position);

                    positions.push(position);
            });

            if(this.position.user_has_invoice_position == null)
                    this.position.user_has_invoice_position = positions;
                else
                    this.position.user_has_invoice_position = this.position.user_has_invoice_position.concat(positions);

            console.log("After addStudents: ");
            console.log(this.position.user_has_invoice_position);
            //console.log("Added students. Students:");
            //console.log(this.position.students);
        },
        splitEveryone: function() {
            /**
             * Teilt Betrag aus dem Betrag-Feld auf alle Schüler auf.
             */
            alert(
                "Folgender Betrag wird auf alle Schüler aufgeteilt: " +
                    this.amount_st
            );

            let number_of_students = this.position.user_has_invoice_position.length;

            alert("Schülerzahl: " + number_of_students);

            let value = this.amount_st / number_of_students;

            alert("Betrag pro Schüler: " + value);

            if (this.type == "overwrite") {
                this.position.user_has_invoice_position.forEach(function(student) {
                    student.amount = value;
                });
            } else {
                this.position.user_has_invoice_position.forEach(function(student) {
                    student.amount += value;
                });
            }
        },

        splitSelected: function() {
            /**
             * Teilt den Betrag aus dem Betrag-Feld auf alle ausgewählten Schüler auf
             */

            alert(
                "Folgender Betrag wird auf ausgewählte Schüler aufgeteilt: " +
                    this.amount_st
            );

            let number_of_students = 0;
            this.position.user_has_invoice_position.forEach(function(student) {
                if (student.checked) {
                    number_of_students++;
                }
            });

            alert("Schülerzahl: " + number_of_students);

            let value = this.amount_st / number_of_students;

            alert("Betrag pro Schüler: " + value);

            if (this.type == "overwrite") {
                this.position.user_has_invoice_position.forEach(function(student) {
                    if (student.checked) {
                        student.amount = value;
                    }
                });
            } else {
                this.position.user_has_invoice_position.forEach(function(student) {
                    if (student.checked) {
                        student.amount += value;
                    }
                });
            }
        },
        assignEveryone: function() {
            alert(
                "Folgender Betrag wird allen Schülern zugewiesen: " +
                    this.amount_st
            );

            let value = parseFloat(this.amount_st);

            if (this.type == "overwrite") {
                this.position.user_has_invoice_position.forEach(function(student) {
                    student.amount = value;
                });
            } else {
                this.position.user_has_invoice_position.forEach(function(student) {
                    student.amount += value;
                });
            }
        },
        assignSelected: function() {
            /*
             * Weist den Betrag aus dem Betrag-Feld allen ausgewählten Schülern zu.
             */
            alert(
                "Folgender Betrag wird ausgewählten Schülern zugewiesen: " +
                    this.amount_st
            );

            let value = this.amount_st;

            if (this.type == "overwrite") {
                this.position.user_has_invoice_position.forEach(function(student) {
                    if (student.checked) {
                        student.amount = value;
                    }
                });
            } else {
                this.position.user_has_invoice_position.forEach(function(student) {
                    if (student.checked) {
                        student.amount += value;
                    }
                });
            }
        },
        removeStudent: function(id) {

            this.position.user_has_invoice_position = this.position.user_has_invoice_position.filter(obj => {
                if (obj.user.id === id) {
                    obj.user.checked = false;
                    return false;
                }
                else
                {
                    return true;
                }
            });

            console.log(this.position.user_has_invoice_position);

        }
    }
};
</script>
