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
                <label for="iban">IBAN (falls notwendig)</label>
                <input
                    type="text"
                    class="form-control"
                    id="iban"
                    placeholder="IBAN"
                    name="iban"
                    v-model="iban"
                />
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
            iban: "",
            reason: "",
            invoicePositions: [],
            id: 0
        };
    },
    methods: {
        addPos: function() {
            /*
                    Aufbau einer invoicePosition:

                    {
                        id: 1,
                        name: "Skikurs",
                        students: [
                            {object} <- id, lastname, firstname, group, amount, annotation
                            {object}
                            {object}
                            ...
                        ]
                    }

                    Feature Ideen:
                        global schüler hinzufügen button: Schüler werden allen Rechnungspos hinzugefügt
                        wenn neue Rechnungspos geöffnet wird: Prompt ob neu oder aus prescribing
                */
            var name = prompt("Namen der Rechnungspos eingeben", "");

            this.id = this.id + 1;
            var id = this.id;

            var position = {
                id: id,
                name: name,
                students: []
            };

            this.invoicePositions.push(position);
        },

        store: function() {
            var that = this;
            var invoicePositionsStripped = [];
            this.invoicePositions.forEach(function(position) {
                invoicePositionsStripped.push({
                    "id": position.id,
                    "name": position.name,
                    "amount": position.amount,
                    "annotation": position.annotation,
                    "belegNr": position.belegNr,
                    "studentIDs": [],
                    "studentAmounts": [],
                    "studentAnnotations": []
                });

                console.log(JSON.stringify(invoicePositionsStripped));

                position.students.forEach(function(student) {
                    invoicePositionsStripped[position.id - 1].studentIDs.push(
                        student.id
                    );

                    invoicePositionsStripped[
                        position.id - 1
                    ].studentAmounts.push(student.amount);

                    invoicePositionsStripped[
                        position.id - 1
                    ].studentAnnotations.push(student.annotation);
                });
            });

            var data = {
                "author": this.author,
                "iban": this.iban,
                "date": this.date,
                "reason": this.reason,
                "invoicePositions": invoicePositionsStripped
            };

            console.log(JSON.stringify(invoicePositionsStripped));
            console.log(JSON.stringify(data));

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/invoice/new",
                dataType: "json",
                data: that.data,
                success: function(response) {
                    alert(response);
                    //  alert("Erfolgreich gespeichert");
                },
                error: function(xhr, status, error) {
                    alert(xhr.responseText);
                    var respJson = JSON.parse(xhr.responseText);
                    that.errors = respJson.errors;
                }
            });
        }
    }
};
</script>

<style></style>
