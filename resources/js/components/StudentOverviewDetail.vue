<template>
    <tr>
        <td>{{ number +1 }}</td>
        <td>{{ this.student.last_name }}</td>
        <td>{{ this.student.first_name }}</td>
        <td>{{ getGroupName(this.student.group_id) }}</td>
        <td>
            <input
                type=""
                name=""
                class="form-control text-right"
                :value="Math.round(invoice_amount * 100) / 100"
                disabled
            />
        </td>
        <td>
            <input type="text" disabled :value="Math.round(prescribing_amount * 100) / 100" class="form-control text-right"/>
        </td>
        <td>
            <input type="text" disabled :value="Math.round(difference * 100) / 100" class="form-control text-right">
        </td>
        <td>
            <span style="font-weight: bold; color: red;" v-if="$parent.$parent.prescribing != null && difference < 0">Gutschrift</span>
            <span style="font-weight: bold; color: red;" v-if="$parent.$parent.prescribing != null && difference > 0">Nachzahlung</span>
            <span style="font-weight: bold;" v-if="$parent.$parent.prescribing != null && difference == 0">Ausgeglichen</span>
        </td>
        <td @click="removeStudent()"><i class="fas fa-user-minus"></i></td>
    </tr>
</template>

<script>
export default {
    mounted() {
        console.log("Component mounted: StudentOverviewInvoice");
    },
    props: ["student", "number"],
    data: function() {
        return {
            prescribing_amount: 0,
            invoice_amount: 0,
        };
    },
    computed: {
      difference: function(){
        return this.invoice_amount - this.prescribing_amount;
      },
    },
    methods: {
        removePrescribing: function(){
            this.prescribing_amount = 0;
        },
        importPrescribing: function(prescribing) {

            this.prescribing_amount = prescribing.amount;
            
            /*
            this.$parent.$parent.prescribing.positions.forEach(position => {
                if (position.user_id === this.student.id) {
                    this.prescribing_amount = position.amount;
                }
            });
            */
        },
        sumOfPositions: function() {
            var sum = 0;

            this.$parent.$parent.invoice.positions.forEach(position => {
                position.studentAmounts.forEach(studentA => {
                    if (studentA.student.id == this.student.id) {
                        sum += parseFloat(studentA.amount);
                    }
                });
            });

            this.invoice_amount = sum;
        },
        removeStudent: function() {
            console.log("deleting student with id " + this.student.id);
            this.$emit("removeStudent", this.student.id);
        },
        getGroupName: function(id) {
            for (var i = 0; i < this.$parent.groupLength; i++) {
                if (this.$parent.groups[i]["id"] == id) {
                    return this.$parent.groups[i]["name"];
                }
            }

            console.log("Could not find groupId: " + id);
            return "Error";
        }
    }
};
</script>
