<template>
    <div class="">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <position-tab v-for="pos in invoicePositions" v-bind:key="pos.id" :position="pos"></position-tab>
                <a class="nav-item nav-link" id="nav-add-tab" data-toggle="tab" href="#nav-add" role="tab" aria-controls="nav-add" aria-selected="false" @click="addPos();">+</a>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <invoice-position v-for="pos in invoicePositions" v-bind:key="pos.id" :position="pos"></invoice-position>
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
