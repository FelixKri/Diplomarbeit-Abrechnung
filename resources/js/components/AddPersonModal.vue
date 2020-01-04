<template>
  <div :id="'addUser_'+id" class="modal fade" role="dialog">
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
                <button type="button" class="btn btn-primary" @click="resetFilter">Filter löschen</button>
              </td>
            </tr>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
          <button type="button" class="btn btn-success" @click="addStudents" data-dismiss="modal">Hinzufügen</button>
        </div>

        <div class="modal-body">
          <button type="button" class="btn btn-primary" @click="selectAll">Alle auswählen</button>
          <button type="button" class="btn btn-primary" @click="selectNone">Keinen auswählen</button>
        </div>
        <div class="modal-body" v-bind:key="student['id'] + id" v-for="student in this.studentsLoaded">
          <input type="checkbox" :id="student['id'] + 'i' + id" />
          {{ student["first_name"] + " " + student["last_name"] + " | " + getGroupName(student['group_id']) }}
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
      type: "POST",
      url: "/getAllGroups",
      dataType: "json",
      data: {},

      success: function(response) {
        that.$parent.groups = response;

        //console.log(that.$parent.groups);

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
      //studentsLoaded: this.$parent.studentsLoaded,
      //studentsDom: this.$parent.studentsDom,
      //studentsLoadedLength: this.$parent.studentsLoadedLength
    };
  },
  props: ["id"],
  methods: {
    /*
    getStudentAfterId: function(id) {
      /*
       * Helper Function, wird für das Hinzufügen oder Entfernen von Schülern aus dem StudentsDOM Array verwendet
       

      for (var i = 0; i < studentsLoadedLength; i++) {
        if (studentsLoaded[i]["id"] == id) return studentsLoaded[i];
      }

      //Fatal error, id not found
      console.log(
        "Error: could not find id: '" + id + "' in getStudentAfterId"
      );
    },*/
    /*getStudentIndexAfterId: function(id) {
      /*
       * Helper Function, wird für das Hinzufügen oder Entfernen von Schülern aus dem StudentsDOM Array verwendet
       
      //No way around it, count studentsDom
      var studentsDom = this.$parent.studentsDom;
      var count = 0;
      for (var thing in studentsDom) {
        count++;
      }

      for (var i = 0; i < count; i++) {
        if (studentsDom[i]["id"] == id) return i;
      }

      //Fatal error, id not found
      console.log(
        "Error: could not find id: '" + id + "' in getStudentIndexAfterId"
      );
    },*/
    getGroupName: function(groupId) {
      for (var i = 0; i < this.$parent.groupLength; i++) {
        if (this.$parent.groups[i]["id"] == groupId)
          return this.$parent.groups[i]["name"];
      }
      //Should not get here, pretty much an error
      return "Unbekannt";
    },
    /*cbClicked: function(id) {
      /*
       *  Wird ausgelöst wenn der Status des Checkmarks neben einem Schüler verändert wird.
       *  Fügt hinzu/entfernt den jeweiligen Schüler aus dem StudentsDOM Array
       */

      /*
      if ($("#" + id + "i" + this.id)[0].checked) {
        //Checked
        this.$parent.studentsDom.push(this.getStudentAfterId(id));
      } else {
        //unchecked
        //find out what index to splice
        this.$parent.studentsDom.splice(this.getStudentIndexAfterId(id));
      }
      
    },*/
    getStudentsList: function() {
      /*
       * Sendet eine POST Request an /getUsers mit den gesetzten Filtern und erhält die Ausgewählten Schüler zurück.
       */
       /*
       var cbs = [];

        if (this.$parent.students > 0)
        {
          for(var i = 0; i < this.$parent.students.length;i++)
          {
            //console.log("Getting cb:");
            //console.log("'" + (studentsLoaded[i]["id"] + 'i' + this.id) + "'");
            cbs.push($("#" + this.studentsLoaded[i]["id"] + 'i' + this.id));
          }
        }
        */

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
        for (var thing in that.studentsLoaded){
          count++;
        }
        that.studentsLoadedLength = count;

        var studentsAlreadyIn = [];

        //console.log("Students loaded length: " + that.studentsLoadedLength);

        for(var j = 0;j < that.studentsLoadedLength;j++)
        {
          var thing = that.studentsLoaded[j];
          //Check if in parent.students
          if(that.$parent.getStudents() != null)
          {
            var studentsCount = 0;

            for(var s in that.$parent.getStudents())
              studentsCount++;

            for(var i = 0;i < studentsCount;i++)
            {
              var student = that.$parent.getStudents()[i];

              if(student["id"] == thing["id"])
              {
                //Add to array
                studentsAlreadyIn.push(student["id"]);
              }
            }
          }
        }

        that.$nextTick(() => 
          {
            //console.log("Already in students: ");
            //console.log(studentsAlreadyIn);

            for(var i = 0;i < studentsAlreadyIn.length;i++)
            {
              var num = studentsAlreadyIn[i];

              //console.log("Found already added student id: " + num);

              //console.log("getting: " + "#" + num + "i" + that.id);

              var cb = $("#" + num + "i" + that.id)[0];

              //cnsole.log("cb:");
              //console.log(cb);
                    
              cb.checked = true;
              cb.disabled = true;
              //that.$("#" + student["id"])[0]["checked"] = true;
              //that.$("#" + student["id"])[0]["disabled"] = true;
            }
          });

        }
      });

    },
    addStudents: function() {
      /*
        Triggert die funktion addStudents in app.js(?)
      */
      
      var eventStudents = [];

      for(var i = 0;i < this.studentsLoadedLength;i++)
      {
        var student = this.studentsLoaded[i];

        var cb = $("#" + student["id"] + 'i' + this.id);

        if(cb[0].checked && !(cb[0].disabled))
        {
          eventStudents.push(student);
        }
      }

      console.log("Emitting addstudents event");
      console.log(eventStudents);
      
      this.$emit("addstudents", eventStudents);

      //Reset filters and clear everything else
      $("#nameFilter" + this.id)[0]["value"] = "";
      $("#classFilter" + this.id)[0]["value"] = "";

      this.getStudentsList();
      //Todo show message like "Users added" ?
    },
    resetFilter: function() {
      $("#nameFilter" + this.id)[0]["value"] = "";
      $("#classFilter" + this.id)[0]["value"] = "";
      this.getStudentsList();
    },
    selectAll: function() {
      //DON'T USE FOREACH IT DOESNT WORK
      for (var i = 0; i < this.studentsLoadedLength; i++) {
        var student = this.studentsLoaded[i];
        $("#" + student.id + "i" + this.id)[0].checked = true;
      }
    },
    selectNone: function() {
      //DON'T USE FOREACH IT DOESNT WORK
      for (var i = 0; i < this.studentsLoadedLength; i++) {
        var student = this.studentsLoaded[i];
        $("#" + student.id + "i" + this.id)[0].checked = false;
      }
    }
  }
};
</script>
