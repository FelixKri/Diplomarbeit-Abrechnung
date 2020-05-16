<template>
    <div class="container">
        <h1>Abrechnungsansicht:</h1>
        <input
            type="button"
            value="Bearbeitung aktivieren"
            class="btn btn-danger"
            @click="edit = true"
            :disabled="edit == true || (this.invoice.approved == true && this.invoice.saved == true)"
        />
        <p>
            Ursprünglicher Author:
            <span style="font-weight: bold">{{
                    this.invoice.author.username
            }}</span>
        </p>
        <div class="form-group">
            <label for="title">Abrechnungsgrund*: </label>
            <select
                name="reason"
                id=""
                class="form-control"
                v-model="invoice.reason.title"
                :disabled="edit == false"
                @change="reasonChanged($event.target.value)"
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
            <label for="date">Datum der Abrechnung*: </label>
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
            <label for="date">Spätest gewünschtes Einzahlungsdatum*: </label>
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
            <label for="description">Anmerkungen: </label>
            <textarea
                type="text"
                name="description"
                id=""
                class="form-control"
                v-model="invoice.annotation"
                rows="5"
                :disabled="edit == false"
            />
            <label for="description">Anmerkung für Nachzahlungen: </label>
            <textarea
                type="text"
                name="description"
                id=""
                class="form-control"
                v-model="annotationAdditional"
                rows="2"
                :disabled="edit == false"
            />
            <label for="description">Anmerkung für Gutschriften: </label>
            <textarea
                type="text"
                name="description"
                id=""
                class="form-control"
                v-model="annotationCredit"
                rows="2"
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
                        invoice.saved == false ||
                            invoice.approved == true ||
                            edit == true
                    "
                />
                <input
                    type="button"
                    value="Freigeben (Lehrer)"
                    class="btn btn-success"
                    @click="setFinished"
                    :disabled="invoice.saved == true ||
                            edit == true"
                />
                <input
                    type="button"
                    value="Zurückweisen"
                    class="btn btn-danger"
                    @click="reject"
                    :disabled="
                        invoice.saved == false ||
                            invoice.approved == true ||
                            edit == true
                    "
                />
                <input
                    type="button"
                    value="Drucken"
                    class="btn btn-primary"
                    @click="print"
                    :disabled="edit == true"
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
            annotationAdditional: "",
            annotationCredit: "",
            edit: false,
            groups: [],
            groupLength: 0,
            invoice: {},
            errors: {},
            last_id: null,
            //Needed because overview needs to hook to an object that is already here when loading
            students: [],
            prescribings: []
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

            return Math.round(totalAmt * 100) / 100;
        }
    },
    methods: {
        removePrescribing: function() {
            (this.prescribings = []), this.$refs.overview.removePrescribing();
        },
        importPrescribings: function(prescribings) {

            this.$refs.overview.importPrescribing(prescribings);
        
        },
        reasonChanged: function(event){
            if(!window.confirm("Aus Vorschreibung übernehmen?"))
                return;

            this.getPrescribings(event);
        },
        getPrescribings: function(event) {

            let reasonId = null;

            this.reason_list.forEach(function(reason) {
                if (event === reason.title) {
                    reasonId = reason.id;
                }
            });

            var that = this;

            axios
                .get("/prescribing/getByReasonId/" + reasonId)
                .then(response => {

                    console.log("Prescribings:");
                    console.log(response.data[0].positions);
                    
                    if(response.data.length == 0)
                        return;

                    //Add the students from the prescribings
                    response.data[0].positions.forEach(function(prescribing){
                        //No need to add students in detail

                        //that.addStudent(prescribing["user"]);

                    });

                    that.prescribings = response.data[0].positions;

                    that.$nextTick(() => {
                    that.importPrescribings(that.prescribings);
                    });
                })
                .catch(error => console.log(error));

            /*
            this.prescribing = {
                positions: [],
            }

            console.log(event);

            let reasonId = null;

            this.reason_list.forEach(function(reason) {
                if (event === reason.title) {
                    reasonId = reason.id;
                }
            });

            axios
                .get("/prescribing/getByReasonId/" + reasonId)
                .then(response => {
                    console.log(response);

                    let compoundPrescribingRaw = {
                        positions: []
                    };

                    response.data.forEach(function(prescribing) {
                        prescribing.positions.forEach(function(position) {
                            compoundPrescribingRaw.positions.push({
                                amount: 0,
                                user_id: position.user_id
                            });
                        });
                    });

                    let compoundPrescribing = {
                        positions: []
                    };

                    let seen = [];

                    compoundPrescribingRaw.positions.forEach(function(position){
                        if(!seen.includes(position.user_id)){
                            compoundPrescribing.positions.push({
                                user_id: position.user_id,
                                amount: 0,
                            });

                            seen.push(position.user_id);
                        }
                    });

                    console.log(compoundPrescribing);

                    response.data.forEach(function(prescribing){
                        prescribing.positions.forEach(function(position){
                            
                            compoundPrescribing.positions.forEach(function(p){
                                if(p.user_id === position.user_id){
                                    p.amount += Number(position.amount)
                                }
                            });

                        });
                    })

                    this.importPrescribing(compoundPrescribing);
                })
                .catch(error => console.log(error));
                */
        },
        removeStudent: function(id) {

            if(this.edit == false)
                return;

            this.students = this.students.filter(el => el.id !== id);
            //this.invoice.students = this.invoice.students.filter(el => el.id !== id);

            //Call removeStudent on the Poses
            for (var i = 0; i < this.invoice.positions.length; i++) {
                this.invoice.positions[
                    i
                ].studentAmounts = this.invoice.positions[
                    i
                ].studentAmounts.filter(el => el.student.id !== id);
            }
        },
        addStudent: function(student){
            if(this.students.filter(e => e["id"] === student["id"]).length > 0)
                {
                    //Duplicate, should not happen
                    return;
                }

            //Add a single student
            this.students.push(student);

            for (var i = 0; i < this.invoicePositions.length; i++) {
                    this.invoicePositions[
                        i
                    ].studentAmounts.push({amount:0, student: student});
                }
        },
        getStudents: function() {
            //console.log("Students:");
            //console.log(this.students);
            return this.students;
        },
        addStudents: function(studentsDom) {
            //console.log("Adding Students: ");
            //console.log(studentsDom);

            var that = this;

            if (this.students == null) {
                //console.log("students null");
                this.students = studentsDom;

                for (var i = 0; i < this.invoice.positions.length; i++) {
                    this.invoice.positions[i].studentAmounts = [];
                    studentsDom.forEach(function(student) {
                        that.invoice.positions[i].studentAmounts.push({ amount: 0, student: student });
                });
                    
                }
            } else {

                studentsDom.forEach(function(student) {
                    that.students.push(student);
                    //posStudentAmount.push({ amount: 0, student: student });
                });

                for (var i = 0; i < this.invoice.positions.length; i++) {
                    
                    /*posStudentAmount.forEach(function(studentA){
                        that1.invoice.positions[i].studentAmounts.push(
                        studentA);
                    });*/
                    studentsDom.forEach(function(student) {
                        that.invoice.positions[i].studentAmounts.push({ amount: 0, student: student });
                    //posStudentAmount.push({ amount: 0, student: student });
                });
                    

                    //console.log("New Position studentAmounts:");
                    //console.log(this.invoice.positions[i].studentAmounts);
                }
            }

            setTimeout(function() {
                that.importPrescribing(that.prescribing);
            }, 100);
        },
        numWithSeperators: function(num) {
            if (num == null) return 0;
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return num_parts.join(",");
        },
        addPos: function() {
            if (this.edit == true) {
                var name = prompt("Namen der Rechnungspos eingeben", "");
                if (name != null) {
                    while (
                        name === "" ||
                        this.invoice.positions.filter(e => e.name === name)
                            .length > 0
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
                    //console.log("Invoice:");
                    //console.log(apiRes.data); // Could be success or error
                    var newInvoice = apiRes.data;
                    this.last_id =
                        newInvoice.positions[
                            newInvoice.positions.length - 1
                        ].position_id;

                    //Cast stuff  from database into usable stuff
                    var tempStudents = [];

                    newInvoice.positions.forEach(function(position) {
                        position.studentAmounts = [];

                        //console.log("Position");
                        //console.log(position);

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
                                tempStudents.push(uhip["user"]);
                            }
                        });

                        delete position.user_has_invoice_position;
                    });

                    this.students = tempStudents;

                    this.invoice = newInvoice;

                    if(this.invoice.imported_prescribing == 1)
                    {
                        this.getPrescribings(this.invoice.reason.title);
                    }
                }
            })();
        },
        release: function() {
            /*
            if (this.invoice.saved == true && this.invoice.approved == false) {
                axios
                    .post("/invoice/release/" + this.id)
                    .then(response => alert(response["data"]))
                    .catch(error => console.log(error));
            }*/
            var that = this;
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/invoice/release/" + that.id,
                dataType: "json",
                data: {
                    annotationAdditional: that.annotationAdditional,
                    annotationCredit: that.annotationAdditional
                },
                success: function(response) {
                    console.log(response);
                    alert(response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    var respJson = JSON.parse(xhr.responseText);
                    that.errors = respJson.errors;
                    //console.log(that.errors);
                    //alert(that.errors);
                }
            });
        },
        print: function() {
            if(this.edit == true)
            {
                return;
            }
            //this.store();

            window.location.href = "/invoice/download/" + this.id;
            //Todo: Sende Request an PDF Generator Funktion im BackEnd
        },
        reject: function() {
            axios
                .post("/invoice/reject/" + this.id)
                .then(response => {
                    alert(response["data"]);
                    location.reload();
                })
                .catch(error => console.log(error));
        },
        setFinished: function() {

            axios
                .post("/invoice/setFinished/" + this.id)
                .then(response => {
                    console.log(response);
                    alert("Erfolgreich freigegeben");
                    location.reload();
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

            if(this.prescribings.length > 0)
                var imported = 1;
            else
                var imported = 0;

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/invoice/update",
                dataType: "json",
                data: {
                    id: that.invoice.id,
                    imported_prescribing: imported,
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
                    location.reload();
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
