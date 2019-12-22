<template>
    <div :id="'getFromPrescribing_' + id" class="modal fade" role="dialog">
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
                                <td @click="copyStudentsToInvoicePosition(prescribing.positions)">{{prescribing.title}}</td>
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
            prescribings: []
        };
    },
    methods: {
        copyStudentsToInvoicePosition: function(positions){

            var students = [];
            positions.forEach(pos => {
                students.push(pos.user);
            });

            students.forEach(st => {
                st.amount = 0;
                st.checked = false;
            });

            console.log(students);
            this.$emit("addstudents", students);
        }
    }
};
</script>

<style></style>
