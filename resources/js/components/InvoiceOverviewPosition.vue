<template>
    <div
        style="position: relative;"
        class="tab-pane fade show"
        :id="'nav-overview'"
        role="tabpanel"
        :aria-labelledby="'nav-overview-tab'"
    >
        <h2 style="">Übersicht</h2>
        
        
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">X</th>
                    <th scope="col">ID</th>
                    <th scope="col">Nachname</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Klasse</th>
                    <th scope="col">Abgerechneter Betrag</th>
                    <th scope="col">Vorgeschriebener Betrag</th>
                    <th scope="col">Betrag</th>
                </tr>
            </thead>
            <tbody>
                <student-invoice
                    v-bind:key="studentA.student.id"
                    v-for="studentA in this.studentAmounts"
                    :studentAmount="studentA"
                    v-on:removeStudent="removeStudent($event)"
                ></student-invoice>

            </tbody>
        </table>

    </div>
</template>

<script>
export default {
    data: function() {
        return {
            prescribing: null,
        };
    },
    props: ["studentAmounts", "groups", "groupLength"],
    methods:{
        removeStudent: function(studentId){
            this.$emit("removeStudent", studentId);
        }
    },
};
</script>

<style></style>
