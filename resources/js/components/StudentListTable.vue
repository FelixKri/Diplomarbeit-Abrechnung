<template>
    <div>
    <table>
        <tr>
            <td>
                    <input type="number" id="number" placeholder="Betrag" class="form-control" v-model="amount_st">
            </td>
            <td>
                <input class="btn btn-primary btn-sm" @click="splitEveryone();" type="button" value="Auf alle Aufteilen">
            </td>
            <td>
                <input class="btn btn-primary btn-sm" @click="splitSelected();" type="button" value="Auf ausgewählte Aufteilen">
            </td>
            <td>
                <input class="btn btn-primary btn-sm" @click="assignEveryone();" type="button" value="Betrag allen zuweisen">
            </td>
            <td>
                <input class="btn btn-primary btn-sm" @click="assignSelected();" type="button" value="Betrag ausgewählten zuweisen ">
            </td>
        </tr>
        <tr>
            <td>
                <input type="radio" id="type" name="type" value="overwrite" checked v-model="type">
                <label for="type">Überschreiben</label>
            </td>
            <td>
                <input type="radio" id="type" name="type" value="add" v-model="type">
                <label for="type">Hinzuaddieren</label>
            </td>
        </tr>
    </table>
    <table class="table">
        
        <thead>
            <tr>
                <th scope="col">X</th>
                <th scope="col">Nr.</th>
                <th scope="col">Klasse</th>
                <th scope="col">Nachname</th>
                <th scope="col">Vorname</th>
                <th scope="col">Betrag</th>
                <th scope="col">Anmerkung</th>
            </tr>
        </thead>
        <tbody>
            <student v-for="student in data.students" v-bind:id="student.id" v-bind:key="student.id" :student="student" v-on:removeStudent="removeStudent($event);"></student>
        </tbody>
    </table>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted: StudentListTable')   

        },
        data: function () {
            return {
                data: this.$parent,
                amount_st: 0,
                type: "overwrite"
            }
        },
        methods: {
            splitEveryone: function(){
                /**
                 * Teilt Betrag aus dem Betrag-Feld auf alle Schüler auf.
                 */
                alert("Folgender Betrag wird auf alle Schüler aufgeteilt: " + this.amount_st);

                let number_of_students = this.data.students.length;

                alert("Schülerzahl: " + number_of_students);

                let value = this.amount_st / number_of_students;

                alert("Betrag pro Schüler: " + value);

                if(this.type=="overwrite"){
                    this.data.students.forEach(function(student) {
                        student.amount = value;
                    });
                }else{
                    this.data.students.forEach(function(student) {
                        student.amount+=value;
                    });
                }
                

            },

            splitSelected: function(){
                /**
                 * Teilt den Betrag aus dem Betrag-Feld auf alle ausgewählten Schüler auf
                 */

                alert("Folgender Betrag wird auf ausgewählte Schüler aufgeteilt: " + this.amount_st);

                let number_of_students = 0;
                this.data.students.forEach(function(student) {
                    if(student.checked){
                        number_of_students++;
                    }
                });

                alert("Schülerzahl: " + number_of_students);

                let value = this.amount_st / number_of_students;

                alert("Betrag pro Schüler: " + value);

                if(this.type=="overwrite"){
                    this.data.students.forEach(function(student) {
                        if(student.checked){
                            student.amount = value;
                        }
                    });
                }else{
                    this.data.students.forEach(function(student) {
                        if(student.checked){
                            student.amount += value;
                        }
                    });
                }

            },
            assignEveryone: function(){

                alert("Folgender Betrag wird allen Schülern zugewiesen: " + this.amount_st);

                let value = parseFloat(this.amount_st);

                if(this.type=="overwrite"){
                    this.data.students.forEach(function(student) {
                        student.amount = value;
                    });
                }else{
                    this.data.students.forEach(function(student) {
                        student.amount += value;
                    });
                }

            },
            assignSelected: function(){
                /*
                * Weist den Betrag aus dem Betrag-Feld allen ausgewählten Schülern zu.
                */
                alert("Folgender Betrag wird ausgewählten Schülern zugewiesen: " + this.amount_st);

                let value = this.amount_st;

                if(this.type=="overwrite"){
                    this.data.students.forEach(function(student) {
                        if(student.checked){
                            student.amount = value;
                        }
                    });
                }else{
                    this.data.students.forEach(function(student) {
                        if(student.checked){
                            student.amount += value;
                        }
                    });
                }


            },
            removeStudent: function(id){
                /*
                 * Wird getriggert von einem Event das von Student.vue gesendet wird, wenn der delete Button geklickt wird.   
                */


                //TODO: In vue sollte man das nicht seperat machen müssen. 

                
                //remove from DOM
                //This removes the checkbox only, not the student
                //$("#"+id).remove();
                this.data.studentsDom.filter(el => el.id !== id);
                //Also remove from studentsLoaded
                this.data.studentsLoaded.filter(el => el.id !== id);
                $( "#" + id )[0].checked = false;

                //Maybe: Also reload students in addPerson, because it is still show now, only unchecked
                //Maybe this is a feature, not a bug tho
                //>>> This is a feature . 

                //remove from Data
                this.data.students = this.data.students.filter(el => el.id !== id);
            }
        }
    }
    
</script>
