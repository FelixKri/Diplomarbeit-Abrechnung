<template>
    <div
        class="tab-pane fade show"
        :id="'nav-' + position.name"
        role="tabpanel"
        :aria-labelledby="'nav-' + position.name + '-tab'"
    >
        <h2>{{ position.name }}</h2>

        <div class="row">
            <div class="col-sm">
                <div class="form-group">
                    <label for="amount">Gesamtbetrag der Position [€]</label>
                    <input
                        type="number"
                        class="form-control"
                        placeholder="Betrag"
                        disabled
                        :value="numWithSeperators(totalAmountComputed)"
                    />
                </div>
                <input type="checkbox" v-model="position.paidByTeacher" :disabled="$parent.edit == false"><span>Von Lehrpersonal bezhalt</span>

                <div class="form-group" v-if="position.paidByTeacher">
                    <label for="iban">IBAN</label>
                    <input
                        type="text"
                        class="form-control"
                        id="iban"
                        placeholder="IBAN"
                        name="iban"
                        v-model="position.iban"
                        :disabled="$parent.edit == false"
                    />
                    <ul
                        v-if="
                            errors[
                                'invoicePositions.'.concat(
                                    this.position.id,
                                    '.iban'
                                )
                            ]
                        "
                        class="alert alert-danger"
                        style="margin: 1em 0;"
                    >
                        <span
                            v-bind:key="errors[
                                'invoicePositions.'.concat(
                                    this.position.id,
                                    '.iban'
                                )
                            ].id">
                            {{ errors[
                                'invoicePositions.'.concat(
                                    this.position.id,
                                    '.iban'
                                )
                            ] }}
                            <br>
                        </span>
                    </ul>
                </div>

                <div class="form-group">
                    <label for="billnumber">BelegNr</label>
                    <input
                        type="number"
                        class="form-control"
                        placeholder="Belegnummer"
                        v-model="position.document_number"
                        :disabled="$parent.edit == false"
                    />
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <label for="annotation">Anmerkungen</label>
                    <textarea
                        class="form-control"
                        id="annotation"
                        rows="5"
                        v-model="position.annotation"
                        :disabled="$parent.edit == false"
                    ></textarea>
                </div>
            </div>
        </div>
        <table>
            <tr>
                <td>
                    <input
                        type="number"
                        id="number"
                        placeholder="Betrag"
                        class="form-control"
                        v-model="amount_st"
                        :disabled="$parent.edit == false"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="splitEveryone()"
                        type="button"
                        value="Auf alle Aufteilen"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="splitSelected()"
                        type="button"
                        value="Auf ausgewählte Aufteilen"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="assignEveryone()"
                        type="button"
                        value="Betrag allen zuweisen"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="assignSelected()"
                        type="button"
                        value="Betrag ausgewählten zuweisen "
                    />
                </td>
            </tr>
            <tr>
                <td>
                    <input
                        type="radio"
                        id="type"
                        name="type"
                        value="overwrite"
                        checked
                        v-model="type"
                    />
                    <label for="type">Überschreiben</label>
                </td>
                <td>
                    <input
                        type="radio"
                        id="type"
                        name="type"
                        value="add"
                        v-model="type"
                    />
                    <label for="type">Hinzuaddieren</label>
                </td>
            </tr>
        </table>
        
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">X</th>
                    <th scope="col">ID</th>
                    <th scope="col">Nachname</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Klasse</th>
                    <th scope="col">Betrag</th>
                </tr>
            </thead>
            <tbody>
                <student-invoice-detail
                    v-bind:key="studentA.student.id"
                    v-for="studentA in position.studentAmounts"
                    :studentAmount="studentA"
                ></student-invoice-detail>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    created: function() {
        console.log("Component created: InvoicePosition");
    },
    data: function() {
        return {
            amount_st: 0,
            type: false
        };
    },
    props: ["position", "errors", "groups", "groupLength"],
    computed: {
        totalAmountComputed: function() {
            let totalAmt = 0;

            if(this.position.studentAmounts == null)
                return;

            this.position.studentAmounts.forEach(function(studentA) {
                totalAmt += Number(studentA.amount);
            });

            return totalAmt;
        }
    },
    methods: {
        numWithSeperators: function(num) {
            if(num == null)
                return 0;
            
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return num_parts.join(",");
        },
        splitEveryone: function() {
            /**
             * Teilt Betrag aus dem Betrag-Feld auf alle Schüler auf.
             */
            

            let number_of_students = this.position.studentAmounts.length;
            let value = this.amount_st / number_of_students;

            alert("Folgender Betrag wird auf " + number_of_students + " Schüler aufgeteilt: " + this.amount_st + "\n Betrag pro Schüler: " + value);

            var splitMoney = 0;

            if (this.type == "overwrite") {
                this.position.studentAmounts.forEach(function(studentAmount) {
                    var studentMoney = Math.round(value * 100) / 100;
                    studentAmount.amount = studentMoney;
                    splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                });
            } else {
                this.position.studentAmounts.forEach(function(studentAmount) {
                    var studentMoney = Math.round(value * 100) / 100;
                    studentAmount.amount += studentMoney;
                    splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                });
            }

            //CENTAUSGLEICH
                //Round centdiff because 100 - 99.99 is apparently 0.0100000000000000000005116
                var centdiff = Math.round((this.amount_st - splitMoney) * 10000) / 10000;
                console.log("centdiff: " + centdiff);
                console.log("splitted money: " + splitMoney);
                console.log("all money: " + this.amount_st);

                if(centdiff > 0)
                {
                    this.position.studentAmounts.forEach(function(studentA) {
                            
                        if(centdiff <= 0)
                        {
                            return;
                        }

                        //Same here, 33.33 + .01 = 33,339999999999996
                        studentA.amount = Math.round((studentA.amount + 0.01) * 100) / 100;
                        centdiff = Math.round((centdiff - 0.01) * 100) / 100;
                    });
                }
                else if(centdiff < 0)
                {
                    //Students pay too much
                        this.position.studentAmounts.forEach(function(studentA) {
                            
                        if(centdiff >= 0)
                        {
                            return;
                        }

                        //Same here
                        studentA.amount = Math.round((studentA.amount - 0.01) * 100) / 100;
                        centdiff = Math.round((centdiff + 0.01) * 100) / 100;
                    });
                }
        },
        splitSelected: function() {
            /**
             * Teilt den Betrag aus dem Betrag-Feld auf alle ausgewählten Schüler auf
             */


            let number_of_students = 0;
            this.position.studentAmounts.forEach(function(studentA) {
                if (studentA.student.checked) {
                    number_of_students++;
                }
            });


            let value = this.amount_st / number_of_students;

            alert("Folgender Betrag wird auf " + number_of_students + " ausgewählte Schüler aufgeteilt: " + this.amount_st + "\n Betrag pro Schüler: " + value);

            var splitMoney = 0;

            if (this.type == "overwrite") {
                this.position.studentAmounts.forEach(function(studentA) {
                    if (student.checked) {
                        var studentMoney = Math.round(value * 100) / 100;
                        studentA.amount = studentMoney;
                        splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                    }
                });
            } else {
                this.position.studentAmounts.forEach(function(studentA) {
                    if (student.checked) {
                        var studentMoney = Math.round(value * 100) / 100;
                        studentA.amount += studentMoney;
                        splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                    }
                });
            }

            //CENTAUSGLEICH auf ausgewählte
                //Round centdiff because 100 - 99.99 is apparently 0.0100000000000000000005116
                var centdiff = Math.round((this.amount_st - splitMoney) * 10000) / 10000;
                console.log("centdiff: " + centdiff);
                console.log("splitted money: " + splitMoney);
                console.log("all money: " + this.amount_st);

                if(centdiff > 0)
                {
                    this.position.studentAmounts.forEach(function(studentA) {
                            if(!studentA.student.checked)
                                return;

                        if(centdiff <= 0)
                        {
                            return;
                        }

                        //Same here, 33.33 + .01 = 33,339999999999996
                        studentA.amount = Math.round((studentA.amount + 0.01) * 100) / 100;
                        centdiff = Math.round((centdiff - 0.01) * 100) / 100;
                    });
                }
                else if(centdiff < 0)
                {
                    //Students pay too much
                        this.position.studentAmounts.forEach(function(studentA) {
                            if(!studentA.student.checked)
                                return;

                        if(centdiff >= 0)
                        {
                            return;
                        }

                        //Same here
                        studentA.amount = Math.round((studentA.amount - 0.01) * 100) / 100;
                        centdiff = Math.round((centdiff + 0.01) * 100) / 100;
                    });
                }
        },
        assignEveryone: function() {
            alert(
                "Folgender Betrag wird allen Schülern zugewiesen: " +
                    this.amount_st
            );

            let value = this.amount_st;

            if (this.type == "overwrite") {
                this.position.studentAmounts.forEach(function(studentA) {
                    studentA.amount = parseFloat(value);
                });
            } else {
                this.position.studentAmounts.forEach(function(studentA) {
                    studentA.amount = parseFloat(studentA.amount) + parseFloat(value);
                });
            }
        },
        assignSelected: function() {
            /*
             * Weist den Betrag aus dem Betrag-Feld allen ausgewählten Schülern zu.
             */
            alert(
                "Folgender Betrag wird ausgewählten Schülern zugewiesen: " +
                    this.amount_st
            );

            let value = this.amount_st;

            if (this.type == "overwrite") {
                this.position.studentAmounts.forEach(function(studentA) {
                    if (studentA.student.checked) {
                        studentA.amount = parseFloat(value);
                    }
                });
            } else {
                this.position.studentAmounts.forEach(function(studentA) {
                    if (studentA.student.checked) {
                        studentA.amount = parseFloat(studentA.amount) + parseFloat(value);
                    }
                });
            }
        }
    }
};
</script>
