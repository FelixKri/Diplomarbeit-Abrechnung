<template>
    <table class="table">
        
        <thead>
            <tr>
                <td>
                    <input type="number" id="number" placeholder="Betrag" class="form-control">
                </td>
                <td>
                    <button class="btn btn-primary btn-sm"> Auf alle Aufteilen </button> 
                </td>
                <td>
                    <button class="btn btn-primary btn-sm"> Betrag allen zuweisen </button>
                </td>
                <td>
                    <button class="btn btn-primary btn-sm"> Betrag ausgewählten zuweisen </button>
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
            <student :data="student" v-for="student in data.students" v-bind:key="student.id"></student>
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
                data: this.$parent
            }
        },

    }
    
</script>

<style>

</style>
