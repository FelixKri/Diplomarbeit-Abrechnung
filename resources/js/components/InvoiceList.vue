<template>
    <div class="container">
        <h1>Liste aller Abrechnungen: </h1>
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
                <tr
                    v-for="i in invoices"
                    v-bind:key="i.id"
                    style="cursor: pointer;"
                    @click="redirectToInvoice(i.id)"
                >
                    <th scope="row">{{ i.id }}</th>
                    <td>{{ i.reason}}</td>
                    <td>{{ i.author.username}} </td>
                    <td>{{ i.annotation }}</td>
                    <td>{{ i.date }}</td>
                    <td v-if="i.saved && i.approved == false">
                         <span class="badge badge-primary">von Lehrer Freigegeben</span>
                    </td>
                    <td v-else-if="i.approved">
                        <span class="badge badge-success">von Sekretariat genehmigt</span>
                    </td>
                    <td v-else>
                        <span class="badge badge-danger">Offen</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: [],
    mounted() {
        this.getInvoices();
    },
    data() {
        return {
            invoices: null
        };
    },
    methods: {
        getInvoices: function() {
            axios
                .get("/invoices/list/getInvoices")
                .then(response => (this.invoices = response.data))
                .catch(error => console.log(error));
        },
        redirectToInvoice: function(id) {
            window.location.href = "/invoice/view/" + id;
        }
    }
};
</script>

<style></style>
