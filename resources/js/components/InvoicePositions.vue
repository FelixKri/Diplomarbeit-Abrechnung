<template>
    <div class="">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-item nav-link" :id="'nav-' + position.name + '-tab'" data-toggle="tab" 
                    :href="'#nav-' + position.name" role="tab" :aria-controls="'nav-' + position.name" 
                    aria-selected="true" v-for="position in invoicePositions" :key="position.id">
                    {{position.name}}
                </a>
                <a class="nav-item nav-link" id="nav-add-tab" data-toggle="tab" href="#nav-add" role="tab" aria-controls="nav-add" aria-selected="false" @click="addPos();">+</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show" :id="'nav-' + position.name" role="tabpanel" :aria-labelledby="'nav-' + position.name + '-tab'"
                v-for="position in invoicePositions" :key="position.id">
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
        </div>        
    </div>
</template>



<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            addPos: function(){
                var name = prompt("Namen der Rechnungspos eingeben", "");
                
                this.id = this.id + 1;
                var id = this.id;

                var position = {
                    id: id,
                    name: name    
                };

                this.invoicePositions.push(position);
            }
        },
        data: function () {
            return {
                invoicePositions: [],
                id: 0
            }
        },
    }
</script>
