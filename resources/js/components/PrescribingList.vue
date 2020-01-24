<template>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Titel</th>
                <th scope="col">Ersteller</th>
                <th scope="col">Beschreibung</th>
                <th scope="col">Fällig bis</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="p in prescribings" v-bind:key="p.id" style="cursor: pointer;" @click="redirectToPrescribing(p.id)">
                <th scope="row">{{p.id}}</th>
                <td>{{p.title}}</td>
                <td>{{p.author_id}}</td>
                <td>{{p.description}}</td>
                <td>{{p.due_until}}</td>
                <td v-if="p.finished">
                    <span class="badge badge-primary">Freigabe steht aus</span>
                </td>
                <td v-else-if="p.approved">
                    <span class="badge badge-success">Genehmigt</span>
                </td>
                <td v-else>
                    <span class="badge badge-danger">Offen</span>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: [],
    mounted() {
        this.getPrescribings();
    },
    data() {
        return {
            prescribings: null
        };
    },
    methods: {
        getPrescribings: function() {
            axios
                .get("/prescribing/list/getPrescribings")
                .then(response => (this.prescribings = response.data))
                .catch(error => console.log(error));
        },
        redirectToPrescribing: function(id){
            window.location.href = '/prescribing/view/'+id;
        },
    }
};
</script>

<style></style>
