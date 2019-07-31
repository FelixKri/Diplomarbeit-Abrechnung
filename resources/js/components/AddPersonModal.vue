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
                <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
                    <button type="button" class="btn btn-success" @click="addStudents">Hinzufügen</button>
            </div>

            <div class="modal-body">
                <button type="button" class="btn btn-primary" @click="selectAll">Alle auswählen</button>
                <button type="button" class="btn btn-primary" @click="selectNone">Keinen auswählen</button> 
            </div>
            <div class="modal-body" v-bind:key="student['id']" v-for="student in data.studentsLoaded" >
                <input type="checkbox" :id="student['id']" @change="cbClicked(student['id'])">{{ student["first_name"] + " " + student["last_name"] + " | " + getGroupName(student['group_id']) }}
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
            getStudentAfterId: function(id)
            {
                for(var i = 0;i < this.$parent.studentsLoadedLength;i++)
                {
                    if(this.$parent.studentsLoaded[i]["id"] == id)
                        return this.$parent.studentsLoaded[i];
                }

                //Fatal error, id not found
                console.log("Error: could not find id: '"+ id + "' in getStudentAfterId");
            },
            getStudentIndexAfterId: function(id)
            {
                //No way around it, count studentsDom
                var count = 0;
                for(var thing in this.$parent.studentsDom)
                {
                    count++;
                }

                for(var i = 0;i < count;i++)
                {
                    if(this.$parent.studentsDom[i]["id"] == id)
                        return i;
                }

                //Fatal error, id not found
                console.log("Error: could not find id: '"+ id + "' in getStudentIndexAfterId");
            },
            getGroupName: function(groupId)
            {

                for(var i = 0;i < this.$parent.groupLength;i++)
                {
                    if(this.$parent.groups[i]["id"] == groupId)
                        return this.$parent.groups[i]["name"];
                }
                //Should not get here, pretty much an error
                return "Unbekannt";
            },
            cbClicked: function(id){

                if($( "#" + id )[0].checked)
                {
                    //Checked
                    this.$parent.studentsDom.push(this.getStudentAfterId(id));
                }
                else
                {
                    //unchecked
                    //find out what index to splice
                    this.$parent.studentsDom.splice(this.getStudentIndexAfterId(id));
                }
            },
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
                        //console.log(response);
                        if(that.$parent.studentsLoadedLength > 0)
                        {
                            //Add all students that are checked because they stay on screen
                            var checkedStudents = [];

                            //DON'T USE FOREACH IT DOESNT WORK
                            for(var i = 0;i < that.$parent.studentsLoadedLength;i++)
                            {
                                var student = that.$parent.studentsLoaded[i];
                                
                                if($("#" + student["id"])[0].checked)
                                    checkedStudents.push(student);
                            } 

                            //Add search results after that
                            response.forEach(student => {
                                //Check if student is already in it (if cb is checked)
                                var cb = $("#" + student.id)[0];
                                
                                if(cb == null || ! cb.checked)
                                    checkedStudents.push(student);
                            });
                            
                            that.$parent.studentsLoaded = checkedStudents;
                        }
                        else
                        {
                            that.$parent.studentsLoaded = response;
                        }

                        var count = 0;
                        for(var thing in that.$parent.studentsLoaded)
                            count++;
                        that.$parent.studentsLoadedLength = count;
                    }
                });
            },
            addStudents: function(){
            
                this.$emit('addstudents');

                //Reset filters and clear everything else
                $( "#nameFilter" )[0]["value"] = "";
                $( "#classFilter" )[0]["value"] = "";
                this.getStudentsList();

                //Todo show message like "Users added"
            },
            resetFilter: function(){
                $( "#nameFilter" )[0]["value"] = "";
                $( "#classFilter" )[0]["value"] = "";
                this.getStudentsList();
            },
            selectAll: function(){
                //DON'T USE FOREACH IT DOESNT WORK
                for(var i = 0;i < this.$parent.studentsLoadedLength;i++)
                {
                    var student = this.$parent.studentsLoaded[i];
                    $("#" + student.id)[0].checked = true;
                    this.cbClicked(student.id);
                }
            },
            selectNone: function(){
                //DON'T USE FOREACH IT DOESNT WORK
                for(var i = 0;i < this.$parent.studentsLoadedLength;i++)
                {
                    var student = this.$parent.studentsLoaded[i];
                    $("#" + student.id)[0].checked = false;
                    this.cbClicked(student.id);
                }
            }
        }
    }
</script>
