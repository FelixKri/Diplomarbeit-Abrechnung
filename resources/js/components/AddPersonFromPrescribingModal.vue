<template>
    <div id="getFromPrescribing" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Hinzufügen</h4>
                    <button type="button" class="close" data-dismiss="modal">
                        &times;
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Titel</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="prescribing in prescribings" v-bind:key="prescribing.id" style="cursor: pointer;">
                                <td @click="importPrescribing(prescribing)">{{prescribing.title}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mounted() {
        axios
            .get("/prescribing/list/getPrescribings")
            .then(response => (this.prescribings = response.data))
            .catch(error => console.log(error));
    },
    props: ["id"],
    data: function() {
        return {
            prescribings: null,
        };
    },
    methods: {
        importPrescribing: function(prescribing){
            
            alert("wird emitted");
            
            this.$emit("importPrescribing", prescribing);

        }
    }
};
</script>

<style></style>
