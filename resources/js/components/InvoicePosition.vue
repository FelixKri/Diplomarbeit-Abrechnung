<template>
    <div class="tab-pane fade show" :id="'nav-' + position.name" role="tabpanel" :aria-labelledby="'nav-' + position.name + '-tab'">
                <h2>ID: {{position.id}} | NAME: {{position.name}}</h2>
                
                <div class="row">

                    <div class="col-sm">
                        <div class="form-group">
                            <label for="amount">Betrag</label>
                            <input type="number" class="form-control" placeholder="Betrag">
                        </div>
                        <div class="form-group">
                            <label for="billnumber">BelegNr</label>
                            <input type="number" class="form-control" placeholder="Belegnummer">
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="annotation">Anmerkungen</label>
                            <textarea class="form-control" id="annotation" rows="5"></textarea>
                        </div>
                    </div>
                </div>

                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addUser" type=button>Person(n) hinzufügen</button>

                <add-person-modal></add-person-modal>

                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nachname</th>
                            <th scope="col">Vorname</th>
                            <th scope="col">Klasse</th>
                            <th scope="col">Betrag</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="student in position.students">
                            <th scope="row">{{student.id}}</th>
                            <td>{{student.lastname}}</td>
                            <td>{{student.firstname}}</td>
                            <td>{{student.group}}</td>
                            <td>{{student.amount}}</td>
                        </tr> 
                    </tbody>
                </table>
            </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        props: [
            "position"
        ]
    }
</script>
