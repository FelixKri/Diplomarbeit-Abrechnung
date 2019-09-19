<template>
    <table class="table">
        
        <thead>
            <tr>
                <td>
                    <input type="number" id="number" placeholder="Betrag" class="form-control" v-model="amount_st">
                </td>
                <td>
                    <input class="btn btn-primary btn-sm" @click="splitEveryone();" type="button" value="Auf alle Aufteilen">
                </td>
                <td>
                    <input class="btn btn-primary btn-sm" @click="assignEveryone();" type="button" value="Betrag allen zuweisen">
                </td>
                <td>
                    <input class="btn btn-primary btn-sm" @click="assignSelected();" type="button" value="Betrag ausgewählten zuweisen ">
                </td>
            </tr>
            <tr>
                <th scope="col">Nr.</th>
                <th scope="col">Klasse</th>
                <th scope="col">Nachname</th>
                <th scope="col">Vorname</th>
                <th scope="col" style="width: 100px;">Betrag</th>
                <th scope="col" style="width: 100px;">Anmerkung</th>
            </tr>
        </thead>
        <tbody>
            <student v-for="student in data.students" v-bind:id="student.id" v-bind:key="student.id" :data="student" v-on:removeStudent="removeStudent($event);"></student>
        </tbody>
    </table>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')

           
        },
        data: function () {
            return {
                data: this.$parent,
                amount_st: 0
            }
        },
        methods: {
            splitEveryone: function(){
                /**
                 * Teilt Betrag aus dem Betrag-Feld auf alle Schüler auf.
                 */
                alert("Folgender Betrag wird auf alle Schüler aufgeteilt: " + this.amount_st);

                let number_of_students = this.data.studentsDom.length;

                alert("Schülerzahl: " + number_of_students);

                let value = this.amount_st / number_of_students;

                alert("Betrag pro Schüler: " + value)

                //TODO: Betrag jedem Schüler (vue objekt) zuweisen.
            },

            splitSelected: function(){
                /**
                 * Teiot den Betrag aus dem Betrag-Feld auf alle ausgewählten Schüler auf
                 */

            },
            assignEveryone: function(){
                /**
                 * Weist den Betrag aus dem Betrag-Feld allen Schülern zu
                 */

            },
            assignSelected: function(){
                /*
                * Weist den Betrag aus dem Betrag-Feld allen ausgewählten Schülern zu.
                */
            },
            removeStudent: function(id){
                /*
                 * Wird getriggert von einem Event das von Student.vue gesendet wird, wenn der delete Button geklickt wird.   
                */


                //TODO: In vue sollte man das nicht seperat machen müssen. 

                
                //remove from DOM
                $("#"+id).remove();

                //remove from Data
                this.data.students = this.data.students.filter(el => el.id !== id);
            }
        }
    }
    
</script>
