<template>
    <div class="container">
        <h1>{{ prescribing.title }}</h1>
        <p>
            Ursprünglicher Author:
            <span style="font-weight: bold">{{ prescribing.author.username }}</span>
        </p>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nachname</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Klasse</th>
                    <th scope="col">Betrag</th>
                    <th scope="col">Anmerkung</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="position in prescribing.positions"
                    v-bind:key="position.id"
                >
                    <td>{{ position.user.last_name }}</td>
                    <td>{{ position.user.first_name }}</td>
                    <td>{{ position.user.group.name }}</td>
                    <td>
                        <input type="number" v-model="position.amount" class="form-control">
                    </td>
                    <td>
                        <input type="text" v-model="position.annotation" class="form-control" placeholder="Optionale Anmerkung">
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="button" value="Änderungen Speichern" class="btn btn-success" @click="store">
        <input type="button" value="Änderungen Speichern und Freigeben" class="btn btn-success" @click="store">
    </div>
</template>

<script>
export default {
    props: ["id"],
    mounted() {
        this.getPrescribing(this.id);
    },
    data() {
        return {
            prescribing: null,
        };
    },
    methods: {
       getPrescribing: function(id){
            axios
                .get("/prescribing/view/getPrescribing/"+id)
                .then(response => (this.prescribing = response.data))
                .catch(error => console.log(error));
       },
       store: function(){

       }
    }
};
</script>

<style></style>
