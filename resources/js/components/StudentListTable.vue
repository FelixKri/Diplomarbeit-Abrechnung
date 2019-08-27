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
            <student v-bind:id="student.id" :data="student" :amount="0" v-for="student in data.students" v-bind:key="student.id" v-on:removeStudent="removeStudent($event);"></student>
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
                alert("Folgender Betrag wird auf alle Schüler aufgeteilt: " + this.amount_st);

                let number_of_students = this.data.studentsDom.length;

                alert("Schülerzahl: " + number_of_students);

                let value = this.amount_st / number_of_students;

                alert("Betrag pro Schüler: " + value)
            },
            assignEveryone: function(){

            },
            assignSelected: function(){

            },
            removeStudent: function(id){
                //remove from DOM
                $("#"+id).remove();

                //remove from Data
                this.data.students = this.data.students.filter(el => el.id !== id);
            }
        }
    }
    
</script>
