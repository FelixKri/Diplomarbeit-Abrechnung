<template>
    <div id="addUser" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Hinzufügen</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <table>
                <tr>
                    <td><input type="text" name="nameFilter" id="nameFilter" class="form-control typeahead" placeholder="Name" v-on:keyup="getStudentsList()"></td>
                    <td><input type="text" name="classFilter" id="classFilter" class="form-control typeahead" placeholder="Klasse" v-on:keyup="getStudentsList()"></td>
                    <td><button type="button" class="btn btn-primary" @click="resetFilter">Filter löschen</button></td>
                </tr>
            </table> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" @click="addStudents">Hinzufügen</button>
            </div>

            <div class="modal-body">
                <input type="checkbox" >
                <label>Select all</label>
            </div>
            <div class="modal-body" v-bind:key="student['id']" v-for="student in data.studentsDom" >
                <input type="checkbox">{{ student["first_name"] + " " + student["last_name"] }}
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
            getStudentsList: function(){

            var that = this;
                $.ajax(
                {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/getUsers',
                    dataType: 'json',
                    data: {
                            nameFilter: $( "#nameFilter" )[0]["value"],
                            classFilter: $( "#classFilter" )[0]["value"]
                        },

                    success: function (response) {
                        console.log(response);
                        that.$parent.studentsDom = response;
                    }
                });
            },
            addStudents: function(){
            
                this.$emit('AddStudents', this.$parent.studentsDom);

                //Reset filters and clear everything else
                $( "#nameFilter" )[0]["value"] = "";
                $( "#classFilter" )[0]["value"] = "";
                this.getStudentsList();
            },
            addStudentToDom: function(){
                
            },
            removeStudent: function(st){
            
            },
            resetFilter: function(){
                $( "#nameFilter" )[0]["value"] = "";
                $( "#classFilter" )[0]["value"] = "";
                this.getStudentsList();
            }
        }
    }
</script>
