<template>
    <div :id="'addUser_' + id" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Hinzufügen</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <table>
                        <tr>
                            <td>
                                <input
                                    type="text"
                                    :name="'nameFilter' + this.id"
                                    :id="'nameFilter' + this.id"
                                    class="form-control typeahead"
                                    placeholder="Name"
                                    v-on:keyup="getStudentsList()"
                                />
                            </td>
                            <td>
                                <input
                                    type="text"
                                    :name="'classFilter' + this.id"
                                    :id="'classFilter' + this.id"
                                    class="form-control typeahead"
                                    placeholder="Klasse"
                                    v-on:keyup="getStudentsList()"
                                />
                            </td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-primary"
                                    @click="resetFilter"
                                >
                                    Filter löschen
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-default"
                        data-dismiss="modal"
                    >
                        Schließen
                    </button>
                    <button
                        type="button"
                        class="btn btn-success"
                        @click="addStudents"
                        data-dismiss="modal"
                    >
                        Hinzufügen
                    </button>
                </div>

                <div class="modal-body">
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="selectAll"
                    >
                        Alle auswählen
                    </button>
                    <button
                        type="button"
                        class="btn btn-primary"
                        @click="selectNone"
                    >
                        Keinen auswählen
                    </button>
                </div>
                <div
                    class="modal-body"
                    v-bind:key="student['id'] + id"
                    v-for="student in this.studentsLoaded"
                >
                    <input type="checkbox" :id="student['id'] + 'i' + id" />
                    {{
                        student["first_name"] +
                            " " +
                            student["last_name"] +
                            " | " +
                            getGroupName(student["group_id"])
                    }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        console.log("Component mounted: AddPersonModal");

        var that = this;

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            type: "GET",
            url: "/getGroups",
            dataType: "json",
            data: {},

            success: function(response) {
                that.$parent.groups = response;

                var count = 0;

                for (var thing in that.$parent.groups) {
                    count++;
                }

                that.$parent.groupLength = count;
            }
        });
    },
    data: function() {
        return {
            studentsLoaded: [],
            studentsLoadedLength: 0,
            students: []
        };
    },
    props: ["id"],
    methods: {
        getGroupName: function(groupId) {
            for (var i = 0; i < this.$parent.groupLength; i++) {
                if (this.$parent.groups[i]["id"] == groupId)
                    return this.$parent.groups[i]["name"];
            }
            return "Unbekannt";
        },
        getStudentsList: function() {
            var that = this;
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/getUsers",
                dataType: "json",
                data: {
                    nameFilter: $("#nameFilter" + that.id)[0]["value"],
                    classFilter: $("#classFilter" + that.id)[0]["value"]
                },

                success: function(response) {
                    that.studentsLoaded = response;

                    var count = 0;
                    for (var thing in that.studentsLoaded) {     
                        count++; 
                    }
                    that.studentsLoadedLength = count;

                    var studentsAlreadyIn = [];

                    for (var j = 0; j < that.studentsLoadedLength; j++) {
                        var thing = that.studentsLoaded[j];
                        var parentStudents = that.$parent.getStudents();
                        
                        //Check if in parent.students
                        if (parentStudents != null) {
                            var studentsCount = 0;

                            for (var s in parentStudents) studentsCount++;

                            for (var i = 0; i < studentsCount; i++) {
                                var student = parentStudents[i];

                                if (student["id"] == thing["id"]) {
                                    //Add to array
                                    studentsAlreadyIn.push(student["id"]);
                                }
                            }
                        }
                    }

                    that.$nextTick(() => {
                        for (var i = 0; i < studentsAlreadyIn.length; i++) {
                            var num = studentsAlreadyIn[i];

                            var cb = $("#" + num + "i" + that.id)[0];

                            cb.checked = true;
                            cb.disabled = true;
                        }
                    });
                }
            });
        },
        addStudents: function() {
        /*
          Triggert die funktion addStudents in app.js
        */

            var eventStudents = [];

            for (var i = 0; i < this.studentsLoadedLength; i++) {
                var student = this.studentsLoaded[i];

                var cb = $("#" + student["id"] + "i" + this.id);

                if (cb[0].checked && !cb[0].disabled) {
                    eventStudents.push(student);
                }
            }

            this.$emit("addstudents", eventStudents);

            //Reset filters
            $("#nameFilter" + this.id)[0]["value"] = "";
            $("#classFilter" + this.id)[0]["value"] = "";

            this.getStudentsList();
        },
        resetFilter: function() {
            $("#nameFilter" + this.id)[0]["value"] = "";
            $("#classFilter" + this.id)[0]["value"] = "";
            this.getStudentsList();
        },
        selectAll: function() {
            for (var i = 0; i < this.studentsLoadedLength; i++) {
                var student = this.studentsLoaded[i];
                $("#" + student.id + "i" + this.id)[0].checked = true;
            }
        },
        selectNone: function() {
            for (var i = 0; i < this.studentsLoadedLength; i++) {
                var student = this.studentsLoaded[i];
                $("#" + student.id + "i" + this.id)[0].checked = false;
            }
        }
    }
};
</script>
