<template>
    <div class="container">
        <h1>Vorschreibungs Ansicht:</h1>
        <p>
            Ursprünglicher Autor:
            <span style="font-weight: bold">{{
                prescribing.author.username
            }}</span>
        </p>
        <div class="form-group">
            <label for="title">Titel: </label>
            <input
                type="text"
                name="title"
                class="form-control"
                id=""
                v-model="prescribing.title"
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
                id=""
                v-model="prescribing.created_at"
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
            <label for="due_until"
                >Spätestens gewünschtes Einzahlungsdatum:
            </label>
            <input
                type=""
                name="due_until"
                class="form-control"
                v-model="prescribing.due_until"
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
            <input
                disabled
                type="text"
                v-model="prescribing.reason.title"
                class="form-control"
            />
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
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Nachname</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Klasse</th>
                    <th scope="col">Betrag</th>
                    <th scope="col">Anmerkung</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="position in prescribing.positions"
                    v-bind:key="position.id"
                >
                    <td>{{ position.user.last_name }}</td>
                    <td>{{ position.user.first_name }}</td>
                    <td>{{ position.user.group.name }}</td>
                    <td>
                        <input
                            type="number"
                            v-model="position.amount"
                            class="form-control"
                        />
                    </td>
                    <td>
                        <input
                            type="text"
                            v-model="position.annotation"
                            class="form-control"
                            placeholder="Optionale Anmerkung"
                        />
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
            value="Freigeben"
            class="btn btn-success"
            @click="release"
        />
        <input
            type="button"
            value="Zurückweisen"
            class="btn btn-danger"
            @click="reject"
        />
        <input
            type="button"
            value="Drucken"
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
    },
    data() {
        return {
            prescribing: null,
            errors: {}
        };
    },
    methods: {
        getPrescribing: function(id) {
            axios
                .get("/prescribing/view/getPrescribing/" + id)
                .then(response => (this.prescribing = response.data))
                .catch(error => console.log(error));
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
                    author: that.prescribing.author.username,
                    date: that.prescribing.created_at,
                    due_until: that.prescribing.due_until,
                    reason_suggestion: that.prescribing.reason_suggestion,
                    reason: that.prescribing.reason.title,
                    description: that.prescribing.description,
                    students: studentIds,
                    amount: studentAmounts,
                    annotation: studentAnnotations,
                    positionIds: positionIds,
                },
                success: function(response) {
                    console.log(response);
                    alert("Erfolgreich gespeichert");
                },
                error: function(xhr, status, error) {
                    var respJson = JSON.parse(xhr.responseText);
                    that.errors = respJson.errors;
                }
            });
        },
        print: function(){
            this.store();

            window.location.href = '/prescribing/download/'+this.prescribing.id;
            //Todo: Sende Request an PDF Generator Funktion im BackEnd
        },
        release: function(){
            axios
                .post("/prescribing/setApproved/" + this.id)
                .then(response => console.log(response))
                .catch(error => console.log(error));
        },
        reject: function(){
            axios
                .post("/prescribing/reject/" + this.id)
                .then(response => console.log(response))
                .catch(error => console.log(error));
        }
    }
};
</script>

<style></style>
