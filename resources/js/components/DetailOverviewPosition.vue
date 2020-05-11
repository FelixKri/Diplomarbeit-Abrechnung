<template>
    <div
        style="position: relative;"
        class="tab-pane fade show"
        :id="'nav-overview'"
        role="tabpanel"
        :aria-labelledby="'nav-overview-tab'"
    >
        <h2 style="">Übersicht</h2>
        
        <button type="button" style="position: absolute; right: 0px; top: 0px;" class="btn btn-primary" @click="triggerSumOfPositions">Übersicht berechnen</button>

        
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nachname</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Klasse</th>
                    <th scope="col">Abgerechneter Betrag [€]</th>
                    <th scope="col">Vorgeschriebener Betrag [€]</th>
                    <th scope="col">Differenz [€]</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <student-overview-detail
                    v-bind:key="student.id"
                    v-for="student in this.students"
                    :student="student"
                    v-on:removeStudent="removeStudent($event)"
                    ref="studentOverview"
                ></student-overview-detail>

            </tbody>
        </table>

    </div>
</template>

<script>
export default {
    data: function() {
        return {
            
        };
    },
    props: ["students", "groups", "groupLength"],
    methods:{
        removePrescribing: function(){

            this.$refs.studentOverview.forEach(ref => {
                ref.removePrescribing();
            });

        },
        importPrescribing: function(prescribings){

          if(this.students.length > 0){
                this.$refs.studentOverview.forEach(ref => {
                    //Give only right prescribing
                    prescribings.forEach(function(prescribing){
                        if(prescribing["user_id"] == ref.student["id"])
                        {
                            ref.importPrescribing(prescribing);
                        }
                    });
                    
                });
            }
        },
        triggerSumOfPositions: function(){
            this.$refs.studentOverview.forEach(ref => {
                ref.sumOfPositions();
            })
        },
        removeStudent: function(studentId){
            this.$emit("removeStudent", studentId);
        }
    },
};
</script>

<style></style>
