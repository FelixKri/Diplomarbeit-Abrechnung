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
            <tr v-for="student in data.students">
                <th scope="row">
                    <input type="number" name="students[]" id="" readonly v-bind:value="student.id">
                </th>
                <td>{{ student.group_id }}</td>
                <td>{{ student.last_name }}</td>
                <td>{{ student.first_name }}</td>
                <td>
                    <input type="number" name="amount[]" id="" class="form-control">
                </td>
                <td>
                    <input type="text" name="annotation[]" id="" class="form-control">
                </td>
                <td><i class="fas fa-edit"></i></td>
                <td><i class="fas fa-user-minus"></i></td>
            </tr>
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
