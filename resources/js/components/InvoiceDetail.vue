<template>
    <div class="container">
        <h1>Abrechnungsansicht:</h1>
        <p>
            Ursprünglicher Author:
            <span style="font-weight: bold">{{ invoice.author.username }}</span>
        </p>
        <div class="form-group">
            <label for="title">Abrechnungsgrund: </label>
            <input
                type="text"
                name="title"
                class="form-control"
                id=""
                v-model="invoice.reason"
            />
            <ul
                v-if="errors.title"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li v-for="error in errors.title" v-bind:key="error.id">
                    {{ error }}
                </li>
            </ul>
        </div>
        <div class="form-group">
            <label for="date">Datum der Abrechnung: </label>
            <input
                type="text"
                name="date"
                id=""
                v-model="invoice.date"
                class="form-control"
            />
            <ul
                v-if="errors.date"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li v-for="error in errors.date" v-bind:key="error.id">
                    {{ error }}
                </li>
            </ul>
        </div>
        <div class="form-group">
            <label for="description">IBAN (optional): </label>
            <input
                type="text"
                name="iban"
                id=""
                class="form-control"
                v-model="invoice.iban"
            />
            <ul
                v-if="errors.iban"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li v-for="error in errors.description" v-bind:key="error.id">
                    {{ error }}
                </li>
            </ul>
        </div>
        <div class="form-group">
            <label for="description">Beschreibung: </label>
            <textarea
                type="text"
                name="description"
                id=""
                class="form-control"
                v-model="invoice.annotation"
                rows="5"
            />
            <ul
                v-if="errors.description"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li v-for="error in errors.description" v-bind:key="error.id">
                    {{ error }}
                </li>
            </ul>
        </div>
        <hr />
        <div class="">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <position-tab
                        v-for="pos in invoice.positions"
                        v-bind:key="pos.id"
                        :position="pos"
                    ></position-tab>
                    <a
                        class="nav-item nav-link"
                        id="nav-add-tab"
                        data-toggle="tab"
                        href="#nav-add"
                        role="tab"
                        aria-controls="nav-add"
                        aria-selected="false"
                        @click="addPos()"
                        >+</a
                    >
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <invoice-detail-position
                    v-for="pos in invoice.positions"
                    v-bind:key="pos.id"
                    :position="pos"
                ></invoice-detail-position>
                <input
                type="button"
                value="Änderungen speichern"
                class="btn btn-success"
                @click="store()"
            />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["id"],
    mounted() {
        this.getInvoice(this.id);
    },
    data() {
        return {
            invoice: null,
            errors: {}
        };
    },
    methods: {
        getInvoice: function(id) {
            axios
                .get("/invoices/view/getInvoice/" + id)
                .then(response => (this.invoice = response.data))
                .catch(error => console.log(error));
        },
        store: function() {
            var that = this;
            var invoicePositionsStripped = [];
            var totalAmountRequest = 0;

            this.invoicePositions.forEach(function(position) {
                invoicePositionsStripped.push({
                    "id": invoice.position.id,
                    "name": invoice.position.name,
                    "amount": invoice.position.amount,
                    "annotation": invoice.position.annotation,
                    "belegNr": invoice.position.document_number,
                    "paidByTeacher": invoice.position.paidByTeacher,
                    "iban": invoice.position.iban,
                    "studentIDs": [],
                    "studentAmounts": [],
                });

                totalAmountRequest += invoice.position.amount;

                invoice.position.students.forEach(function(student) {
                    invoicePositionsStripped[invoice.position.id - 1].studentIDs.push(
                        student.id
                    );

                    invoicePositionsStripped[
                        invoice.position.id - 1
                    ].studentAmounts.push(student.amount);
                });
            });

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/invoice/update",
                dataType: "json",
                data: {
                    "id": that.invoice.id,
                    "author": that.author,
                    "date": that.date,
                    "reason": that.reason,
                    "annotation": that.annotation,
                    "totalAmount": totalAmountRequest,
                    "invoicePositions": invoicePositionsStripped
                },
                success: function(response) {
                    //alert("loda")
                    console.log(response);
                    alert("Erfolgreich gespeichert!");
                },
                error: function(xhr, status, error) {
                    var respJson = JSON.parse(xhr.responseText);
                    that.errors = respJson.errors;
                }
            });
        }
    }
};
</script>

<style></style>
