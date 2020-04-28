<template>
    <div class="container">
        <h1>Vorschreibungs Ansicht:</h1>
        <input
            type="button"
            value="Bearbeitung aktivieren"
            class="btn btn-danger"
            @click="edit = true"
            :disabled="edit == true"
        />
        <p>
            Ursprünglicher Autor:
            <span style="font-weight: bold">{{ prescribing.author }}</span>
        </p>
        <div class="form-group">
            <label for="title">Titel: </label>
            <input
                type="text"
                name="title"
                class="form-control"
                v-model="prescribing.title"
                :disabled="edit == false"
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
            <label for="date">Datum der Vorschreibung: </label>
            <input
                type="text"
                name="date"
                v-model="prescribing.date"
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
            <label for="due_until"
                >Spätestens gewünschtes Einzahlungsdatum:
            </label>
            <input
                type=""
                name="due_until"
                class="form-control"
                v-model="prescribing.due_until"
                :disabled="edit == false"
            />
            <ul
                v-if="errors.due_until"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li v-for="error in errors.due_until" v-bind:key="error.id">
                    {{ error }}
                </li>
            </ul>
        </div>
        <div class="form-group">
            <label for="reason_suggestion">Grundvorschlag: </label>
            <input
                type="text"
                name="reason_suggestion"
                class="form-control"
                v-model="prescribing.reason_suggestion"
                :disabled="edit == false"
            />
            <ul
                v-if="errors.reason_suggestion"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li
                    v-for="error in errors.reason_suggestion"
                    v-bind:key="error.id"
                >
                    {{ error }}
                </li>
            </ul>
        </div>
        <div class="form-group">
            <label for="reason">Grund</label>
            <select
                name="reason"
                class="form-control"
                v-model="prescribing.reason"
                :disabled="edit == false"
            >
                <option value=""> </option>
                <option
                    v-for="reason in reasons"
                    :value="reason"
                    v-bind:key="reason.id"
                    >{{ reason }}</option
                >
            </select>
            <ul
                v-if="errors.reason"
                class="alert alert-danger"
                style="margin: 1em 0;"
            >
                <li v-for="error in errors.reason" v-bind:key="error.id">
                    {{ error }}
                </li>
            </ul>
        </div>
        <div class="form-group">
            <label for="description">Beschreibung: </label>
            <input
                type="text"
                name="description"
                id=""
                class="form-control"
                v-model="prescribing.description"
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
            <label for="total_amount">Gesamtbetrag [€]</label>
            <input
                type="number"
                name="total_amount"
                :value="numWithSeperators(totalAmountComputed)"
                disabled
                class="form-control"
            />
        </div>
        <button
            class="btn btn-primary btn-sm"
            data-toggle="modal"
            :data-target="'#addUser_1'"
            type="button"
            :disabled="edit == false"
        >
            Person(n) hinzufügen
        </button>
        <add-person-modal
            v-on:addstudents="addStudents"
            :id="1"
        ></add-person-modal>
        <hr />
        <table>
            <tr>
                <td>
                    <input
                        type="number"
                        id="number"
                        placeholder="Betrag"
                        class="form-control"
                        v-model="amount_st"
                        :disabled="edit == false"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="splitEveryone()"
                        type="button"
                        value="Auf alle Aufteilen"
                        :disabled="edit == false"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="splitSelected()"
                        type="button"
                        value="Auf ausgewählte Aufteilen"
                        :disabled="edit == false"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="assignEveryone()"
                        type="button"
                        value="Betrag allen zuweisen"
                        :disabled="edit == false"
                    />
                </td>
                <td>
                    <input
                        class="btn btn-primary btn-sm"
                        @click="assignSelected()"
                        type="button"
                        value="Betrag ausgewählten zuweisen "
                        :disabled="edit == false"
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
                        :disabled="edit == false"
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
                        :disabled="edit == false"
                    />
                    <label for="type">Hinzuaddieren</label>
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th scope="col">X</th>
                    <th scope="col">Nachname</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Klasse</th>
                    <th scope="col">Betrag</th>
                    <th scope="col">Anmerkung</th>
                    <th scope="col">X</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="position in prescribing.positions"
                    v-bind:key="position.id"
                >
                    <td>
                        <input type="checkbox" v-model="position.checked" />
                    </td>
                    <td>{{ position.user.last_name }}</td>
                    <td>{{ position.user.first_name }}</td>
                    <td>{{ position.user.group.name }}</td>
                    <td>
                        <input
                            type=""
                            name=""
                            class="form-control text-right"
                            v-model="position.amount"
                            :disabled="edit == false"
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            v-model="position.annotation"
                            class="form-control"
                            placeholder="Optionale Anmerkung"
                            :disabled="edit == false"
                        />
                    </td>
                    <td
                        style="cursor: pointer;"
                        @click="removeStudent(position.id)"
                    >
                        <i class="fas fa-user-minus"></i>
                    </td>
                </tr>
            </tbody>
        </table>
        <input
            type="button"
            value="Änderungen Speichern"
            class="btn btn-primary"
            @click="store"
        />
        <input
            type="button"
            value="Freigeben (Sekretariat)"
            class="btn btn-success"
            @click="release()"
            :disabled="
                prescribingRequested.finished == false ||
                    prescribingRequested.approved == true
            "
        />
        <input
            type="button"
            value="Freigeben (Lehrer)"
            class="btn btn-success"
            @click="setFinished()"
            :disabled="prescribingRequested.finished == true"
        />
        <input
            type="button"
            value="Zurückweisen"
            class="btn btn-danger"
            @click="reject()"
            :disabled="
                prescribingRequested.finished == false ||
                    prescribingRequested.approved == true
            "
        />
        <input
            type="button"
            value="Speichern und Drucken"
            class="btn btn-primary"
            @click="print"
        />
    </div>
</template>

<script>
export default {
    props: ["id"],
    mounted() {
        this.getPrescribing(this.id);
        this.getAllReasons();
        this.getAllGroups();

        console.log("Prescribing:");
        console.log(this.prescribing);
    },
    data() {
        return {
            edit: false,
            prescribingRequested: null,
            prescribing: {
                id: null,
                title: "",
                date: null,
                due_until: null,
                reason_suggestion: "",
                reason: "",
                description: "",
                positions: [],  
                author: ""
            },
            reasons: null,
            groups: null,
            errors: {},
            amount_st: 0,
            type: "overwrite"
        };
    },
    computed: {
        totalAmountComputed: function() {
            let totalAmt = 0;

            this.prescribing.positions.forEach(function(student) {
                totalAmt += Number(student.amount);
            });

            return totalAmt;
        }
    },
    methods: {
        numWithSeperators: function(num) {
            var num_parts = num.toString().split(".");
            num_parts[0] = num_parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            return num_parts.join(",");
        },
        getAllReasons: function() {
            axios
                .get("/getReasons")
                .then(response => (this.reasons = response.data))
                .catch(error => console.log(error));
        },
        getAllGroups: function(){
                axios
                .get("/getGroups")
                .then(response => (this.groups = response.data))
                .catch(error => console.log(error));
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
                    this.prescribingRequested = apiRes.data;
                    this.prescribing.title = this.prescribingRequested.title;
                    this.prescribing.date = this.prescribingRequested.date;
                    this.prescribing.due_until = this.prescribingRequested.due_until;
                    this.prescribing.reason_suggestion = this.prescribingRequested.reason_suggestion;
                    if (this.prescribingRequested.reason) {
                        this.prescribing.reason = this.prescribingRequested.reason.title;
                    }

                    this.prescribing.description = this.prescribingRequested.description;
                    this.prescribing.positions = this.prescribingRequested.positions;
                    this.prescribing.author = this.prescribingRequested.author.username;
                    this.prescribing.id = this.prescribingRequested.id;
                }
            })();
        },
        getStudents: function() {
            return this.prescribing.positions;
        },
        addStudents: function(studentsDom) {

            var newPoses = [];

            var that = this;

            studentsDom.forEach(function(student){
                var newPos = {};
                newPos["id"] = -1;
                newPos["user_id"] = student["id"];
                newPos["amount"] = 0;
                newPos["annotation"] = null;
                newPos["user"] = student;

                for(var i = 0;i < Object.keys(that.groups).length;i++)
                {

                 if(that.groups[i].id == student.group_id)
                    {
                        student["group"] = that.groups[i];
                        break;
                    }   
                }

                newPoses.push(newPos);
            });
            console.log(newPoses);

            if (this.prescribing.positions == null) 
                this.prescribing.positions = newPoses;
            else 
                this.prescribing.positions = this.prescribing.positions.concat(newPoses);
        },
        removeStudent: function(id) {
            this.prescribing.positions = this.prescribing.positions.filter(
                el => el.id !== id
            );
        },
        store: function() {
            var that = this;
            var studentIds = [];
            var studentAmounts = [];
            var studentAnnotations = [];
            var positionIds = [];

            this.prescribing.positions.forEach(function(position) {
                positionIds.push(position.id);
                studentIds.push(position.user_id);
                studentAmounts.push(position.amount);
                studentAnnotations.push(position.annotation);
            });

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/prescribing/update",
                dataType: "json",
                data: {
                    id: that.prescribing.id,
                    title: that.prescribing.title,
                    author: that.prescribing.author,
                    date: that.prescribing.date,
                    due_until: that.prescribing.due_until,
                    reason_suggestion: that.prescribing.reason_suggestion,
                    reason: that.prescribing.reason,
                    description: that.prescribing.description,
                    students: studentIds,
                    amount: studentAmounts,
                    annotation: studentAnnotations,
                    positionIds: positionIds
                },
                success: function(response) {
                    alert("Erfolgreich gespeichert");
                    this.errors.author = null;
                    this.errors.description = null;
                    this.errors.date = null;
                    this.errors.due_until = null;
                    this.errors.reason = null;
                    this.errors.reason_suggestion = null;
                },
                error: function(xhr, status, error) {
                    var respJson = JSON.parse(xhr.responseText);
                    that.errors = respJson.errors;
                }
            });
        },
        print: function() {
            this.store();

            window.location.href =
                "/prescribing/download/" + this.prescribing.id;
            //Todo: Sende Request an PDF Generator Funktion im BackEnd
        },
        release: function() {
            this.store();
            axios
                .post("/prescribing/release/" + this.id)
                .then(response => {
                    console.log(response);
                    alert("Erfolgreich genehmigt");
                })
                .catch(error => console.log(error));
        },
        reject: function() {
            this.store();
            axios
                .post("/prescribing/reject/" + this.id)
                .then(response => {
                    console.log(response);
                    alert("Erfolgreich zurückgewiesen");
                })
                .catch(error => console.log(error));
        },
        setFinished: function() {
            axios
                .post("/prescribing/setFinished/" + this.id)
                .then(response => {
                    console.log(response);
                    alert("Erfolgreich freigegeben");
                })
                .catch(error => console.log(error));

            //TODO: Speicher Button disablen, da freigegebene Prescribings nicht mehr editiert werden können
        },
        splitEveryone: function() {
            /**
             * Teilt Betrag aus dem Betrag-Feld auf alle Schüler auf.
             */

            let number_of_students = this.prescribing.positions.length;

            let value = this.amount_st / number_of_students;

            alert(
                "Folgender Betrag wird auf " +
                    number_of_students +
                    " Schüler aufgeteilt: " +
                    this.amount_st +
                    "\n Betrag pro Schüler: " +
                    value
            );

            var splitMoney = 0;

            if (this.type == "overwrite") {
                this.prescribing.positions.forEach(function(student) {
                    var studentMoney = Math.round(value * 100) / 100;
                    student.amount = studentMoney;
                    splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                });
            } else {
                this.prescribing.positions.forEach(function(student) {
                    var studentMoney = Math.round(value * 100) / 100;
                    student.amount = Math.round((student.amount + studentMoney) * 100) / 100;
                    splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                });
            }

            //CENTAUSGLEICH
            //Round centdiff because 100 - 99.99 is apparently 0.0100000000000000000005116
            var centdiff =
                Math.round((this.amount_st - splitMoney) * 10000) / 10000;
            console.log("centdiff: " + centdiff);
            console.log("splitted money: " + splitMoney);
            console.log("all money: " + this.amount_st);

            if (centdiff > 0) {
                this.prescribing.positions.forEach(function(student) {
                    if (centdiff <= 0) {
                        return;
                    }

                    //Same here, 33.33 + .01 = 33,339999999999996
                    student.amount =
                        Math.round((student.amount + 0.01) * 100) / 100;
                    centdiff = Math.round((centdiff - 0.01) * 100) / 100;
                });
            } else if (centdiff < 0) {
                //Students pay too much
                this.prescribing.positions.forEach(function(student) {
                    if (centdiff >= 0) {
                        return;
                    }

                    //Same here
                    student.amount =
                        Math.round((student.amount - 0.01) * 100) / 100;
                    centdiff = Math.round((centdiff + 0.01) * 100) / 100;
                });
            }

            //Just for information: the program will not reach this point after centausgleich
            //Because return was used instead of break
        },

        splitSelected: function() {
            /**
             * Teilt den Betrag aus dem Betrag-Feld auf alle ausgewählten Schüler auf
             */

            let number_of_students = 0;
            this.prescribing.positions.forEach(function(student) {
                if (student.checked) {
                    number_of_students++;
                }
            });

            let value = this.amount_st / number_of_students;

            alert(
                "Folgender Betrag wird auf " +
                    number_of_students +
                    " ausgewählte Schüler aufgeteilt: " +
                    this.amount_st +
                    "\n Betrag pro Schüler: " +
                    value
            );

            var splitMoney = 0;

            if (this.type == "overwrite") {
                this.prescribing.positions.forEach(function(student) {
                    if (student.checked) {
                        var studentMoney = Math.round(value * 100) / 100;
                        student.amount = studentMoney;
                        splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                    }
                });
            } else {
                this.prescribing.positions.forEach(function(student) {
                    if (student.checked) {
                        var studentMoney = Math.round(value * 100) / 100;
                        student.amount = Math.round((student.amount + studentMoney) * 100) / 100;
                        splitMoney = Math.round((splitMoney + studentMoney) * 100) / 100;
                    }
                });
            }

            //CENTAUSGLEICH auf ausgewählte
                //Round centdiff because 100 - 99.99 is apparently 0.0100000000000000000005116
                var centdiff = Math.round((this.amount_st - splitMoney) * 100) / 100;
                console.log("centdiff: " + centdiff);
                console.log("splitted money: " + splitMoney);
                console.log("all money: " + this.amount_st);

                if(centdiff > 0)
                {
                    this.prescribing.positions.forEach(function(student) {
                            if(!student.checked)
                                return;

                        if(centdiff <= 0)
                        {
                            return;
                        }

                        //Same here, 33.33 + .01 = 33,339999999999996
                        student.amount = Math.round((student.amount + 0.01) * 100) / 100;
                        centdiff = Math.round((centdiff - 0.01) * 100) / 100;
                    });
                }
                else if(centdiff < 0)
                {
                    //Students pay too much
                        this.prescribing.positions.forEach(function(student) {
                            if(!student.checked)
                                return;

                        if(centdiff >= 0)
                        {
                            return;
                        }

                        //Same here
                        student.amount = Math.round((student.amount - 0.01) * 100) / 100;
                        centdiff = Math.round((centdiff + 0.01) * 100) / 100;
                    });
                }
        },
        assignEveryone: function() {
            alert(
                "Folgender Betrag wird allen Schülern zugewiesen: " +
                    this.amount_st
            );

            let value = parseFloat(this.amount_st);

            if (this.type == "overwrite") {
                this.prescribing.positions.forEach(function(student) {
                    student.amount = value;
                });
            } else {
                this.prescribing.positions.forEach(function(student) {
                    student.amount += value;
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
                this.prescribing.positions.forEach(function(student) {
                    if (student.checked) {
                        student.amount = value;
                    }
                });
            } else {
                this.prescribing.positions.forEach(function(student) {
                    if (student.checked) {
                        student.amount += value;
                    }
                });
            }
        }
    }
};
</script>

<style></style>
