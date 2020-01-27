<template>
    <div class="container">
        <h1>Neue Abrechnung erstellen</h1>
        <form action="/invoice/new" method="post">
            <div class="form-group">
                <label for="reason">Grund</label>
                <input
                    type="text"
                    class="form-control"
                    id="reason"
                    placeholder="Grund der Abrechnung"
                    name="reason"
                    v-model="reason"
                />
                <ul v-if="errors.reason" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.reason" v-bind:key="error.id">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="date">Datum</label>
                <input
                    type="date"
                    class="form-control"
                    id="reason"
                    placeholder="Datum"
                    name="date"
                    v-model="date"
                />
                <ul v-if="errors.date" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.date" v-bind:key="error.id">{{error}}</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="author">Abrechner</label>
                <input
                    type="text"
                    class="form-control"
                    id="author"
                    placeholder="Abrechner"
                    name="author"
                    v-model="author"
                />
            </div>
            <div class="form-group">
                <label for="annotation">Anmerkungen</label>
                <textarea
                    class="form-control"
                    id="annotation"
                    rows="5"
                    v-model="annotation"
                ></textarea>
                <ul v-if="errors.annotation" class="alert alert-danger" style="margin: 1em 0;">
                    <li v-for="error in errors.annotation" v-bind:key="error.id">{{error}}</li>
                </ul>
            </div>

            <div class="">
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <position-tab
                            v-for="pos in invoicePositions"
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
                    <invoice-position
                        v-for="pos in invoicePositions"
                        v-bind:key="pos.id"
                        :position="pos"
                        :errors="errors"
                    ></invoice-position>
                </div>
            </div>

            <input
                type="button"
                value="Speichern"
                class="btn btn-success"
                @click="store()"
            />
        </form>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            author: "admin",
            date: "",
            reason: "",
            annotation: "",
            invoicePositions: [],
            id: 0,
            errors: {},
        };
    },
    computed:{
        totalAmountComputed: function(){
            let totalAmt = 0;

            this.invoicePositions.forEach(function(position){
                totalAmt += position.amount;
            });

            return totalAmt;
        }
    },
    methods: {
        addPos: function() {
            var name = prompt("Namen der Rechnungspos eingeben", "");
            if(name != null){
                while(name === "" || this.invoicePositions.filter(e => e.name === name).length > 0){
                    name = prompt("Bitte den Namen der Rechnungsposition nicht leer lassen oder einen bereits verwendeten Namen eingeben.")
                }
                this.id = this.id + 1;
                var id = this.id;

                var position = {
                    id: id,
                    name: name,
                    document_number: "",
                    annotation: "",
                    amount: 0,
                    paidByTeacher: false,
                    iban: "",
                    students: []
                };

                this.invoicePositions.push(position);
            }
            
        },

        store: function() {
            var that = this;
            var invoicePositionsStripped = [];
            var totalAmountRequest = 0;

            this.invoicePositions.forEach(function(position) {
                invoicePositionsStripped.push({
                    "id": position.id,
                    "name": position.name,
                    "amount": position.amount,
                    "annotation": position.annotation,
                    "belegNr": position.document_number,
                    "paidByTeacher": position.paidByTeacher,
                    "iban": position.iban,
                    "studentIDs": [],
                    "studentAmounts": [],
                });

                totalAmountRequest += position.amount;

                position.students.forEach(function(student) {
                    invoicePositionsStripped[position.id - 1].studentIDs.push(
                        student.id
                    );

                    invoicePositionsStripped[
                        position.id - 1
                    ].studentAmounts.push(student.amount);
                });
            });

            alert(totalAmountRequest);

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/invoice/new",
                dataType: "json",
                data: {
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
