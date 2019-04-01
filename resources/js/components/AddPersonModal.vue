<template>
    <div id="addUser" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Person hinzufügen</h4>
            </div>
            <div class="modal-body">
            <ul>
                <li v-bind:key="student['id']" v-for="student in data.studentsDom">{{ student["first_name"] + " " + student["last_name"] }} <button type="button" class="btn btn-danger btn-sm" style="display: inline; width: 100px; cursor: pointer;" @click="removeStudent(studentName);">entfernen</button></li>
            </ul>
            <table>
                <tr>
                    <td><input type="text" name="user[]" id="user_autocomplete" class="form-control typeahead" placeholder="Name oder ID" @focus="autocomplete()"></td>
                   <td><button type="button" class="btn btn-primary" @click="addStudentToDom">+</button></td>
                </tr>
            </table> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal" @click="addStudents">Hinzufügen</button>
            </div>
        </div>
  
    </div>
  </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        data: function () {
                return {
                    data: this.$parent
                }
            },
        methods: {
            autocomplete: function() {
                console.log("autocomplete function launched");
                $( "#user_autocomplete" ).autocomplete({
                    source: "http://localhost:8000/user/autocomplete/"
                });
            },
            addStudents: function(){
                
                this.$parent.studentsDom.forEach(student => {
                    console.log("Adding student: " + student);
                    this.$parent.students.push(student);
                });

            },
            addStudentToDom: function(){
                var input = $("#user_autocomplete");
                var that = this;

                axios.get('/api/getStudent', {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    params: {
                        user: input.val()
                    }})
                    .then(response =>{
                        console.log("Adding student to DOM: " + response.data);
                        that.$parent.studentsDom.push(response.data);
                    });

                    input.val("");
            },
            removeStudent: function(st){
                    this.$parent.studentsDom = this.$parent.studentsDom.filter(s => s["id"] !== st["id"]);
            }
        }
    }
</script>
