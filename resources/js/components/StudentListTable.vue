<template>
    <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Nr.</th>
                        <th scope="col">Klasse</th>
                        <th scope="col">Nachname</th>
                        <th scope="col">Vorname</th>
                        <th scope="col" style="width: 100px;">Betrag</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="student in data.students">
                        <th scope="row">{{ student.id}}</th>
                        <td>{{ student.group_id }}</td>
                        <td>{{ student.last_name }}</td>
                        <td>{{ student.first_name }}</td>
                        <td><input type="number" name="amount[]" id="" class="form-control"></td>
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
