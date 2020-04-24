<template>
    <div class="container">
        <h1>Abrechnungsansicht:</h1>
        <input
            type="button"
            value="Bearbeitung aktivieren"
            class="btn btn-danger"
            @click="edit = true;"
            :disabled="edit == true"
        />
        <p>
            Ursprünglicher Author:
            <span style="font-weight: bold">{{
                this.invoice.author.username
            }}</span>
        </p>
        <div class="form-group">
            <label for="title">Abrechnungsgrund: </label>
            <select
                name="reason"
                id=""
                class="form-control"
                v-model="invoice.reason.title"
                :disabled="edit == false"
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
                <span v-for="error in errors.reason" v-bind:key="error.id"
                    >{{ error }}<br
                /></span>
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
                :disabled="edit == false"
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
            <label for="date">Spätest gewünschtes Einzahlungsdatum: </label>
            <input
                type="text"
                name="date"
                id=""
                v-model="invoice.due_until"
                class="form-control"
                :disabled="edit == false"
            />
            <ul
                v-if="errors.date"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li v-for="error in errors.due_until" v-bind:key="error.id">
                    {{ error }}
                </li>
            </ul>
        </div>

        <div class="form-group">
            <label for="description">Anmerkungen </label>
            <textarea
                type="text"
                name="description"
                id=""
                class="form-control"
                v-model="invoice.annotation"
                rows="5"
                :disabled="edit == false"
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
        <div class="form-group">
            <label for="total_amount">Gesamtbetrag der Abrechnung [€]</label>
            <input
                type="text"
                name="total_amount"
                class="form-control"
                disabled
                :value="numWithSeperators(totalAmountComputed)"
            />
            <br />
            <button
                class="btn btn-primary btn-sm"
                data-toggle="modal"
                :data-target="'#addUser_1'"
                type="button"
                :disabled="edit == false"
            >
                Person(en) hinzufügen
            </button>
            <button
                class="btn btn-primary btn-sm"
                data-toggle="modal"
                :data-target="'#getFromPrescribing'"
                type="button"
                :disabled="edit == false"
            >
                Personen aus Vorschreibung übernehmen
            </button>
            <span v-if="prescribing != null">
                {{ prescribing.title }}
                <button class="link" href="" @click="removePrescribing()" :disabled="edit == false">
                    Entfernen
                </button></span
            >
        </div>
        <hr />
        <div class="">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
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
                        :disabled="edit == false"
                        >+</a
                    >

                    <add-person-modal
                        v-on:addstudents="addStudents"
                        :id="1"
                    ></add-person-modal>
                    <add-from-prescribing-modal
                        v-on:importPrescribing="importPrescribing"
                    >
                    </add-from-prescribing-modal>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <invoice-detail-overview-position
                    v-on:removeStudent="removeStudent"
                    :students="students"
                    :groups="groups"
                    :groupLength="groupLength"
                    ref="overview"
                ></invoice-detail-overview-position>
                <invoice-detail-position
                    v-for="pos in invoice.positions"
                    v-bind:key="pos.id"
                    :position="pos"
                    :errors="errors"
                    :groups="groups"
                    :groupLength="groupLength"
                ></invoice-detail-position>

                <input
                    type="button"
                    value="Änderungen speichern"
                    class="btn btn-success"
                    @click="store()"
                    :disabled="edit == false"
                />
                <input
                    type="button"
                    value="Freigeben (Sekretariat)"
                    class="btn btn-success"
                    @click="release()"
                    :disabled="
                        invoice.saved == false || invoice.approved == true || edit == false
                    "
                />
                <input
                    type="button"
                    value="Freigeben (Lehrer)"
                    class="btn btn-success"
                    @click="setFinished"
                    :disabled="invoice.saved == true || edit == false"
                />
                <input
                    type="button"
                    value="Zurückweisen"
                    class="btn btn-danger"
                    @click="reject"
                    :disabled="
                        invoice.saved == false || invoice.approved == true || edit == false
                    "
                />
                <input
                    type="button"
                    value="Drucken"
                    class="btn btn-primary"
                    @click="print"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["id", "reason_list"],
    mounted() {
        this.getInvoice(this.id);
    },
    data() {
        return {
            edit: false,
            groups: [],
            groupLength: 0,
            invoice: {},
            errors: {},
            last_id: null,
            //Needed because overview needs to hook to an object that is already here when loading
            students: [],
            prescribing: null
        };
    },
    computed: {
        totalAmountComputed: function() {
            let totalAmt = 0;

            this.invoice.positions.forEach(function(position) {
                if (position.studentAmounts == null) return;

                position.studentAmounts.forEach(function(student) {
                    totalAmt += Number(student.amount);
                });
            });

            return totalAmt;
        }
    },
    methods: {
        removePrescribing: function() {
            (this.prescribing = null), this.$refs.overview.removePrescribing();
        },
        importPrescribing: function(prescribing) {
            alert("wird importiert");

            this.prescribing = prescribing;

            this.$refs.overview.importPrescribing();
        },
        removeStudent: function(id) {
            this.students = this.students.filter(el => el.id !== id);
            this.invoice.students = this.invoice.students.filter(
                el => el.id !== id
            );
            //Call removeStudent on the Poses
            for (var i = 0; i < this.invoice.positions.length; i++) {
                this.invoice.positions[
                    i
                ].studentAmounts = this.invoice.positions[
                    i
                ].studentAmounts.filter(el => el.student.id !== id);
            }
        },
        getStudents: function() {
            return this.invoice.students;
        },
        addStudents: function(studentsDom) {
            console.log("Adding Students: ");
            console.log(studentsDom);

            if (this.invoice.students == null) {
                console.log("students null");
                this.invoice.students = studentsDom;
                this.students = studentsDom;

                var posStudentAmount = [];
                studentsDom.forEach(function(student) {
                    posStudentAmount.push({ amount: 0, student: student });
                });

                for (var i = 0; i < this.invoice.positions.length; i++) {
                    this.invoice.positions[i].studentAmounts = posStudentAmount;
                }
            } else {
                this.invoice.students.push(studentsDom);
                this.students.push(studentsDom);

                var posStudentAmount = [];
                studentsDom.forEach(function(student) {
                    posStudentAmount.push({ amount: 0, student: student });
                });

                for (var i = 0; i < this.invoice.positions.length; i++) {
                    this.invoice.positions[i].studentAmounts.push(
                        posStudentAmount
                    );
                }
            }
        },
        numWithSeperators: function(num) {
            if (num == null) return 0;
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return num_parts.join(",");
        },
        addPos: function() {
            var name = prompt("Namen der Rechnungspos eingeben", "");
            if (name != null) {
                while (
                    name === "" ||
                    this.invoice.positions.filter(e => e.name === name).length >
                        0
                ) {
                    name = prompt(
                        "Bitte den Namen der Rechnungsposition nicht leer lassen oder einen bereits verwendeten Namen eingeben."
                    );
                }

                this.last_id = this.last_id + 1;
                var id = this.id;

                var posStudentAmount = [];
                this.students.forEach(function(student) {
                    posStudentAmount.push({ amount: 0, student: student });
                });

                var position = {
                    id: this.last_id,
                    position_id: this.last_id,
                    name: name,
                    document_number: "",
                    annotation: "",
                    amount: 0,
                    paidByTeacher: false,
                    iban: null,
                    studentAmounts: posStudentAmount
                };

                this.invoice.positions.push(position);
            }
        },
        getInvoice: function(id) {
            (async () => {
                let apiRes = null;
                try {
                    apiRes = await axios.get("/invoices/view/getInvoice/" + id);
                } catch (err) {
                    apiRes = err.response;
                } finally {
                    console.log(apiRes); // Could be success or error
                    var newInvoice = apiRes.data;
                    this.last_id =
                        newInvoice.positions[
                            newInvoice.positions.length - 1
                        ].position_id;

                    //Cast stuff  from database into usable stuff
                    var tempStudents = [];

                    newInvoice.positions.forEach(function(position) {
                        position.studentAmounts = [];

                        console.log(position);

                        position.user_has_invoice_position.forEach(function(
                            uhip
                        ) {
                            var sAmount = {
                                amount: parseFloat(uhip["amount"]),
                                student: uhip["user"]
                            };
                            position.studentAmounts.push(sAmount);

                            var found = false;

                            tempStudents.forEach(function(student) {
                                if (student.id == uhip["user"].id) {
                                    found = true;
                                    return;
                                }
                            });

                            if (!found) {
                                console.log(uhip["user"]);
                                tempStudents.push(uhip["user"]);
                            }
                        });

                        delete position.user_has_invoice_position;
                    });

                    this.students = tempStudents;

                    this.invoice = newInvoice;

                    if (this.invoice.prescribing_id != null) {
                        this.getPrescribing(this.invoice.prescribing_id);
                    }
                }
            })();
        },
        getPrescribing: function(id) {
            (async () => {
                let apiRes = null;
                try {
                    apiRes = await axios.get(
                        "/prescribing/view/getPrescribing/" + id
                    );
                } catch (err) {
                    apiRes = err.response;
                } finally {
                    console.log(apiRes);
                    this.prescribing = apiRes.data;

                    this.$refs.overview.importPrescribing();
                }
            })();
        },
        release: function() {
            this.store();
            if (this.invoice.saved == true && this.invoice.approved == false) {
                axios
                    .post("/invoice/release/" + this.id)
                    .then(response => alert(response["data"]))
                    .catch(error => console.log(error));
            }
        },
        print: function() {
            this.store();

            window.location.href = "/invoice/download/" + this.id;
            //Todo: Sende Request an PDF Generator Funktion im BackEnd
        },
        reject: function() {
            this.store();
            axios
                .post("/invoice/reject/" + this.id)
                .then(response => alert(response["data"]))
                .catch(error => console.log(error));
        },
        setFinished: function() {
            this.store();
            axios
                .post("/invoice/setFinished/" + this.id)
                .then(response => {
                    console.log(response);
                    alert("Erfolgreich freigegeben");
                })
                .catch(error => console.log(error));

            //TODO: Speicher Button disablen, da freigegebene Prescribings nicht mehr editiert werden können
        },

        store: function() {
            var invoicePositionsStripped = [];
            var totalAmountRequest = 0;

            this.invoice.positions.forEach(function(position) {
                var studentIDs = [];
                var studentAmounts = [];

                position.studentAmounts.forEach(function(studentA) {
                    studentIDs.push(studentA.student.id);
                    studentAmounts.push(studentA.amount);
                });

                var paidByTeacher = false;

                if (position.paidByTeacher == 1) paidByTeacher = true;

                invoicePositionsStripped.push({
                    id: position.id,
                    name: position.name,
                    annotation: position.annotation,
                    belegNr: position.document_number,
                    paidByTeacher: paidByTeacher,
                    iban: position.iban,
                    studentIDs: studentIDs,
                    studentAmounts: studentAmounts,
                    position_id: position.position_id
                });

                totalAmountRequest += position.total_amount;
            });

            var that = this;
            //console.log("InvoicePositionsStripped:");
            //console.log(invoicePositionsStripped);

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/invoice/update",
                dataType: "json",
                data: {
                    id: that.invoice.id,
                    prescribing_id: that.prescribing,
                    author: "admin",
                    date: that.invoice.date,
                    due_until: that.invoice.due_until,
                    reason: that.invoice.reason.title,
                    annotation: that.invoice.annotation,
                    totalAmount: that.totalAmountComputed,
                    invoicePositions: invoicePositionsStripped
                },
                success: function(response) {
                    console.log(response);
                    alert("Erfolgreich gespeichert!");
                },
                error: function(xhr, status, error) {
                    var respJson = JSON.parse(xhr.responseText);
                    that.errors = respJson.errors;
                    //console.log(that.errors);
                    //alert(that.errors);
                }
            });
        }
    }
};
</script>

<style></style>
