<template>
<div class="container">
        <form>
            <h1>Neue Vorschreibung erstellen</h1>
            <div class="form-group">
                <label for="title">Titel: </label> 
                <input type="text" name="title" class="form-control" id="" v-model="title">
                <ul v-if="errors.title" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.title">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="author">Vorschreiber: </label> 
                <input type="text" v-model="author" readonly name="author" class="form-control">
                <ul v-if="errors.author" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.author">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="date">Datum der Vorschreibung: </label> 
                <input type="date" name="date" id="" v-model="date" class="form-control">
                <ul v-if="errors.date" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.date">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="due_until">Spätestens gewünschtes Einzahlungsdatum: </label> 
                <input type="date" name="due_until" class="form-control" v-model="due_until">
                <ul v-if="errors.due_until" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.due_until">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="reason_suggestion">Grundvorschlag: </label> 
                <input type="text" name="reason_suggestion" class="form-control" v-model="reason_suggestion">
                <ul v-if="errors.reason_suggestion" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.reason_suggestion">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="reason">Grund</label> 
                <select name="reason" id="" class="form-control" v-model="reason">
                    <option v-for="reason in reasons" :value="reason.title">{{reason.title}}</option>
                </select>
                <ul v-if="errors.reason" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.reason">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="description">Beschreibung: </label> 
                <input type="text" name="description" id="" class="form-control" v-model="description"> 
                <ul v-if="errors.description" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.description">{{error}}</li>
                </ul>
            </div>
            <hr>
            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUser" type=button>Hinzufügen</button>
        
            <add-person-modal v-on:addstudents="addStudents"></add-person-modal>
            
            <student-list-table></student-list-table>

            <input type="button" value="Speichern" class="btn btn-success" @click="store">
        </form>
    </div>
</template>



<script>
    export default {
        props: ["reasons"],
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
                studentsDom: [],
                studentsLoaded: [],
                studentsLoadedLength: 0,
                errors: {}
            }
        },
        methods: {
            store: function(){
                var that = this; //i hate this(that)
                var studentIds = [];
                var studentAmounts = [];
                var studentAnnotations = [];

                this.students.forEach(function(student) {
                    studentIds.push(student.id);
                    studentAmounts.push(student.amount);
                    studentAnnotations.push(student.annotation);
                });

                $.ajax(
                {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/prescribing/new',
                    dataType: 'json',
                    data: {
                        title: that.title,
                        author: that.author,
                        date: that.date,
                        due_until: that.due_until,
                        reason_suggestion: that.reason_suggestion,
                        reason: that.reason,
                        description: that.description,
                        students: studentIds,
                        amount: studentAmounts,
                        annotation: studentAnnotations
                    },
                    success: function (response) {
                        alert("Erfolgreich gespeichert");
                    },
                    error: function(xhr, status, error) {
                        var respJson = JSON.parse(xhr.responseText);
                        that.errors = respJson.errors;
                    }
                });
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
