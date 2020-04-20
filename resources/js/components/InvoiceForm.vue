<template>
    <div class="container">
        <h1>Neue Abrechnung erstellen</h1>
        <form action="/invoice/new" method="post">
            <div class="form-group">
                <div class="form-group">
                    <label for="reason">Grund</label>
                    <select
                        name="reason"
                        id=""
                        class="form-control"
                        v-model="reason"
                    >
                        <option
                            v-for="reason in reason_list"
                            :value="reason.title"
                            v-bind:key="reason.id"
                            >{{ reason.title }}</option
                        >
                    </select>
                    <ul
                        v-if="errors.reason"
                        class="alert alert-danger"
                        style="margin: 1em 0;"
                    >
                        <span
                            v-for="error in errors.reason"
                            v-bind:key="error.id"
                            >{{ error }}<br
                        /></span>
                    </ul>
                </div>
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
                <ul
                    v-if="errors.date"
                    class="alert alert-danger"
                    style="margin: 1em 0;"
                >
                    <span v-for="error in errors.date" v-bind:key="error.id"
                        >{{ error }}<br
                    /></span>
                </ul>
            </div>
            <div class="form-group">
                <label for="date">Spätest gewünschtes Einzahlungsdatum</label>
                <input
                    type="date"
                    class="form-control"
                    id="due_until"
                    placeholder="Datum"
                    name="due_until"
                    v-model="due_until"
                />
                <ul
                    v-if="errors.due_until"
                    class="alert alert-danger"
                    style="margin: 1em 0;"
                >
                    <span
                        v-for="error in errors.due_until"
                        v-bind:key="error.id"
                        >{{ error }}<br
                    /></span>
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
                <ul
                    v-if="errors.annotation"
                    class="alert alert-danger"
                    style="margin: 1em 0;"
                >
                    <span
                        v-for="error in errors.annotation"
                        v-bind:key="error.id"
                        >{{ error }}<br
                    /></span>
                </ul>
            </div>
            <div class="form-group">
                <label for="total_amount">Gesamtbetrag [€]</label>
                <input
                    type="number"
                    name="total_amount"
                    :value="numWithSeperators(totalAmountComputed)"
                    disabled
                    class="form-control"
                />
                <br />
                <button
                    class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    :data-target="'#addUser_1'"
                    type="button"
                >
                    Person(en) hinzufügen
                </button>

                <button
                    class="btn btn-primary btn-sm"
                    data-toggle="modal"
                    data-target="#getFromPrescribing"
                    type="button"
                >
                    Vorschreibung importieren
                </button>
                <add-from-prescribing-modal
                    v-on:importPrescribing="importPrescribing"
                ></add-from-prescribing-modal>
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
                            id="nav-overview-tab"
                            data-toggle="tab"
                            href="#nav-overview"
                            role="tab"
                            aria-controls="nav-overview"
                            aria-selected="false"
                            >Übersicht</a
                        >
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
                    <add-person-modal
                        v-on:addstudents="addStudents"
                        :id="1"
                    ></add-person-modal>
                    <invoice-overview-position
                        v-on:removeStudent="removeStudent"
                        :students="students"
                        :groups="groups"
                        :groupLength="groupLength"
                        ref="overview"
                    ></invoice-overview-position>
                    <invoice-position
                        v-for="pos in invoicePositions"
                        v-bind:key="pos.id"
                        :position="pos"
                        :errors="errors"
                        :groups="groups"
                        :groupLength="groupLength"
                        v-on:removeInvoicePosition="
                            removeInvoicePosition($event)
                        "
                    ></invoice-position>
                </div>
            </div>
            <br />
            <input
                type="button"
                value="Speichern"
                class="btn btn-success"
                @click="store()"
            />
            <input
                type="button"
                value="Freigeben"
                class="btn btn-success"
                @click="release()"
                :disabled="this.saved == false"
            />
        </form>
    </div>
</template>

<script>
export default {
    props: ["reason_list"],
    created: function() {
        $(".nav-tabs a:first").tab("show");
    },
    data: function() {
        return {
            groups: [],
            groupLength: 0,
            id: null,
            author: "admin",
            date: "",
            due_until: "",
            reason: "",
            annotation: "",
            invoicePositions: [],
            invoice_id: null,
            errors: {},
            saved: false,
            students: [],
            prescribing: null,
            last_id: 0
        };
    },
    computed: {
        totalAmountComputed: function() {
            let totalAmt = 0;

            this.invoicePositions.forEach(function(position) {
                position.studentAmounts.forEach(function(studentAmount) {
                    totalAmt += studentAmount.amount;
                });
            });

            return totalAmt;
        }
    },
    methods: {
        importPrescribing: function(prescribing) {
            alert("wird importiert");

            this.prescribing = prescribing;

            this.$refs.overview.importPrescribing();
        },
        removeStudent: function(id) {
            this.students = this.students.filter(el => el.id !== id);
            //Call removeStudent on the Poses
            for (var i = 0; i < this.invoicePositions.length; i++) {
                this.invoicePositions[i].studentAmounts = this.invoicePositions[
                    i
                ].studentAmounts.filter(el => el.student.id !== id);
            }
        },
        getStudents: function() {
            return this.students;
        },
        addStudents: function(studentsDom) {
            console.log("Adding Students: ");
            console.log(studentsDom);

            if (this.students == null) {
                this.students = studentsDom;

                var posStudentAmount = [];
                studentsDom.forEach(function(student) {
                    posStudentAmount.push({ amount: 0, student: student });
                });
                for (var i = 0; i < this.invoicePositions.length; i++) {
                    this.invoicePositions[i].studentAmounts = posStudentAmount;
                }
            } else {
                this.students = this.students.concat(studentsDom);

                var posStudentAmount = [];
                studentsDom.forEach(function(student) {
                    posStudentAmount.push({ amount: 0, student: student });
                });

                for (var i = 0; i < this.invoicePositions.length; i++) {
                    this.invoicePositions[
                        i
                    ].studentAmounts = this.invoicePositions[
                        i
                    ].studentAmounts.concat(posStudentAmount);
                }
            }
        },
        numWithSeperators: function(num) {
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return num_parts.join(",");
        },
        removeInvoicePosition: function(event) {
            alert("loda");
            console.log(event);

            this.invoicePositions = this.invoicePositions.filter(function(obj) {
                return obj.id !== event;
            });
        },
        addPos: function() {
            var name = prompt("Namen der Rechnungspos eingeben", "");
            if (name != null) {
                while (
                    name === "" ||
                    this.invoicePositions.filter(e => e.name === name).length >
                        0
                ) {
                    name = prompt(
                        "Bitte den Namen der Rechnungsposition nicht leer lassen oder einen bereits verwendeten Namen eingeben."
                    );
                }
                this.id = this.id + 1;
                var id = this.id;

                var posStudentAmount = [];
                this.students.forEach(function(student) {
                    posStudentAmount.push({ amount: 0, student: student });
                });

                var position = {
                    id: id,
                    name: name,
                    document_number: "",
                    annotation: "",
                    amount: 0,
                    paidByTeacher: false,
                    iban: "",
                    studentAmounts: posStudentAmount
                };

                this.invoicePositions.push(position);
            }
        },
        release: function() {
            if (this.saved) {
                axios
                    .post("/invoice/setFinished/" + this.id)
                    .then(response => {
                        console.log(response);
                        alert("Erfolgreich freigegeben");
                    })
                    .catch(error => console.log(error));

                //TODO: Speicher Button disablen, da freigegebene Prescribings nicht mehr editiert werden können
            }

            //TODO: Speicher Button disablen, da freigegebene Prescribings nicht mehr editiert werden können
        },
        store: function() {
            this.errors = null;
            this.errors = {};

            var that = this;
            var invoicePositionsStripped = [];
            var totalAmountRequest = 0;

            console.log(this.invoicePositions.length);

            this.invoicePositions.forEach(function(position) {
                invoicePositionsStripped.push({
                    id: position.id,
                    name: position.name,
                    position_id: position.position_id,
                    amount: position.amount,
                    annotation: position.annotation,
                    belegNr: position.document_number,
                    paidByTeacher: position.paidByTeacher,
                    iban: position.iban,
                    studentAmounts: position.studentAmounts
                });

                totalAmountRequest += position.amount;
            });

            console.log(invoicePositionsStripped);

            console.log(that.reason);

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/invoice/new",
                dataType: "json",
                data: {
                    prescribing_id: that.prescribing.id,
                    id: that.invoice_id,
                    author: that.author,
                    date: that.date,
                    due_until: that.due_until,
                    reason: that.reason,
                    annotation: that.annotation,
                    totalAmount: totalAmountRequest,
                    invoicePositions: invoicePositionsStripped
                },
                success: function(response) {
                    console.log(response);
                    that.errors = {};
                    alert("Erfolgreich gespeichert!");
                    that.invoice_id = response;
                    that.saved = true;
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
