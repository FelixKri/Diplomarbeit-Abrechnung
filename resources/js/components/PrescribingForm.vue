<template>
    <div class="container">
        <form>
            <h1>Neue Vorschreibung erstellen</h1>
            <div class="form-group">
                <label for="title">Titel: *</label>
                <input
                    type="text"
                    name="title"
                    class="form-control"
                    id=""
                    v-model="title"
                />
                <ul
                    v-if="errors.title"
                    class="alert alert-danger"
                    style="margin: 1em 0;"
                >
                    <span v-for="error in errors.title" v-bind:key="error.id"
                        >{{ error }}<br /></span
                    ><br />
                </ul>
            </div>
            <div class="form-group">
                <label for="author">Vorschreiber*: </label>
                <input
                    type="text"
                    v-model="author"
                    readonly
                    name="author"
                    class="form-control"
                />
                <ul
                    v-if="errors.author"
                    class="alert alert-danger"
                    style="margin: 1em 0;"
                >
                    <span v-for="error in errors.author" v-bind:key="error.id"
                        >{{ error }}<br
                    /></span>
                </ul>
            </div>

            <div class="form-group">
                <label for="date">Datum der Vorschreibung*:</label>
                <input
                    type="date"
                    name="date"
                    id=""
                    v-model="date"
                    class="form-control"
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
                <label for="due_until"
                    >Spätestens gewünschtes Einzahlungsdatum*: 
                </label>
                <input
                    type="date"
                    name="due_until"
                    class="form-control"
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
                <label for="reason_suggestion">Grundvorschlag**: </label>
                <input
                    type="text"
                    name="reason_suggestion"
                    class="form-control"
                    v-model="reason_suggestion"
                />
                <ul
                    v-if="errors.reason_suggestion"
                    class="alert alert-danger"
                    style="margin: 1em 0;"
                >
                    <span
                        v-for="error in errors.reason_suggestion"
                        v-bind:key="error.id"
                        >{{ error }}<br
                    /></span>
                </ul>
            </div>
            <div class="form-group">
                <label for="reason">Grund**</label>
                <select
                    name="reason"
                    id=""
                    class="form-control"
                    v-model="reason"
                >
                    <option
                        v-for="reason in reasons"
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
                <label for="description">Beschreibung*: </label>
                <input
                    type="text"
                    name="description"
                    id=""
                    class="form-control"
                    v-model="description"
                />
                <ul
                    v-if="errors.description"
                    class="alert alert-danger"
                    style="margin: 1em 0;"
                >
                    <span
                        v-for="error in errors.description"
                        v-bind:key="error.id"
                        >{{ error }}<br
                    /></span>
                </ul>
            </div>
            <div class="form-group">
                <label for="description">Gesamtbetrag [€]: </label>
                <input
                    type="text"
                    name="totalAmount"
                    id=""
                    class="form-control"
                    :value="numWithSeperators(Math.round((totalAmountComputed) * 100) / 100)"
                    disabled
                />
            </div>
            <hr />

            <button
                class="btn btn-primary btn-sm"
                data-toggle="modal"
                :data-target="'#addUser_1'"
                type="button"
            >
                Person(en) hinzufügen
            </button>

            <add-person-modal
                v-on:addstudents="addStudents"
                :id="1"
            ></add-person-modal>

            <student-list-table></student-list-table>

            <input
                type="button"
                value="Speichern"
                class="btn btn-primary"
                @click="store"
                :disabled="released == true"
            />
            <input
                type="button"
                value="Freigeben"
                class="btn btn-success"
                @click="release"
                :disabled="id == null"
            />
            <input
                type="button"
                value="Drucken"
                class="btn btn-success"
                @click="print"
                :disabled="id == null"
            />
        </form>
    </div>
</template>

<script>
export default {
    props: ["reasons"],
    mounted() {
        console.log("Component mounted: PrescribingForm");

        var that = this;

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },

            type: "GET",
            url: "/getReasons",
            dataType: "json",

            success: function(response) {
                this.reasons = response;
            }
        });
    },
    data: function() {
        return {
            released: false,
            id: null,
            title: "",
            author: "admin",
            date: "",
            due_until: "",
            reason_suggestion: "",
            reason: "",
            description: "",
            students: [],
            errors: {},
            groups: [],
            groupLength: 0
        };
    },
    computed: {
        totalAmountComputed: function() {
            let totalAmt = 0;

            this.students.forEach(function(student) {
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
        getStudents: function() {
            return this.students;
        },
        print: function(){
            window.location.href =
                "/prescribing/download/" + this.id;
        },
        store: function() {
            this.errors = null;
            this.errors = {};

            var that = this; //i hate this(that)
            var studentIds = [];
            var studentAmounts = [];
            var studentAnnotations = [];

            this.students.forEach(function(student) {
                studentIds.push(student.id);
                console.log("Pushing amounts by:");
                console.log(student.amount);
                studentAmounts.push(student.amount);
                studentAnnotations.push(student.annotation);
            });

            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                type: "POST",
                url: "/prescribing/new",
                dataType: "json",
                data: {
                    id: that.id,
                    title: that.title,
                    author: that.author,
                    date: that.date,
                    due_until: that.due_until,
                    reason_suggestion: that.reason_suggestion,
                    reason: that.reason,
                    description: that.description,
                    students: studentIds,
                    amount: studentAmounts,
                    annotation: studentAnnotations,
                    totalAmount: that.totalAmountComputed
                },
                success: function(response) {
                    console.log(response);
                    alert("Erfolgreich gespeichert");
                    that.id = response;
                    //window.location = "/";
                    //disable Speicherbutton
                    that.errors = {};
                },
                error: function(xhr, status, error) {
                    var respJson = JSON.parse(xhr.responseText);
                    that.errors = respJson.errors;
                }
            });
        },
        release: function() {

            this.store();
            axios
                .post("/prescribing/setFinished/" + this.id)
                .then(response => {
                    console.log(response);
                    alert("Erfolgreich freigegeben");
                })
                .catch(error => console.log(error));

            this.released = true;
        },
        addStudents: function(studentsDom) {
            //Todo check for duplicates
            //Add to students
            if (this.students == null) this.students = studentsDom;
            else this.students = this.students.concat(studentsDom);
        }
    }
};
</script>
