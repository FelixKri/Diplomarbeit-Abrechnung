<template>
<div class="container">
        <form>
            <h1>Neue Vorschreibung erstellen</h1>

            <label for="title">Titel: </label> 
            <input type="text" name="title" class="form-control" id="" v-bind:value="title">
            <label for="author">Vorschreiber: </label> 
            <input type="text" v-bind:value="author" readonly name="author" class="form-control">
            <label for="date">Datum der Vorschreibung: </label> 
            <input type="date" name="date" id="" v-bind:value="date" class="form-control">
            <label for="due_until">Spätestens gewünschtes Einzahlungsdatum: </label> 
            <input type="date" name="due_until" class="form-control" v-bind:value="due_until">
            <label for="reason_suggestion">Grundvorschlag: </label> 
            <input type="text" name="reason_suggestion" class="form-control" v-bind:value="reason_suggestion">
            <label for="reason">Grund</label> 
            <select name="reason" id="" class="form-control" v-bind:value="reason">
                <option value="0">TODO: reasons per ajax abfragen und hier anzeigen</option>
            </select>
            <label for="description">Beschreibung: </label> 
            <input type="text" name="description" id="" class="form-control" v-bind:value="description"> 
            <hr>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUser" type=button>Hinzufügen</button>
        
            <add-person-modal v-on:addstudents="addStudents"></add-person-modal>
            
            <student-list-table></student-list-table>

            <input type="button" value="Speichern" class="btn btn-success" @click="store();">
        </form>
    </div>
</template>



<script>
    export default {
        mounted() {
            $.ajax({

                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                type: "GET",
                url: '/getReasons',
                dataType: 'json',

                success: function (response) {
                    this.reasons = response;
                }
            });
        },
        data: function () {
            return {
                title: "",
                author: "admin",
                date: "",
                due_until: "",
                reason_suggestion: "",
                reason: "",
                reasons: [],
                description: "",
                students: [],
            }
        },
        methods: {
            store: function(){

            },
            addStudents: function(studentsDom)
            {

                //Todo check for duplicates
                //Add to students
                this.students = studentsDom;
            }
        },
    }
</script>
