<template>
    <tr>
        <td>
            <input
                type="checkbox"
                @click="switchChecked()"
                :disabled="$parent.$parent.edit == false"
            />
        </td>
        <th scope="row">{{ studentAmount.student.id }}</th>
        <td>{{ studentAmount.student.last_name }}</td>
        <td>{{ studentAmount.student.first_name }}</td>
        <td>{{ getGroupName(studentAmount.student.group_id) }}</td>
        <td>
            <input
                type="number"
                name=""
                class="form-control text-right"
                :disabled="$parent.$parent.edit == false"
                v-model="studentAmount.amount"
            />
        </td>
    </tr>
</template>

<script>
export default {
    mounted() {
        console.log("Component mounted: Student");
        this.studentAmount.student.checked = false;
    },
    props: ["studentAmount"],
    data: function() {
        return {};
    },
    methods: {
        switchChecked: function() {
            if (this.studentAmount.student.checked == false) {
                this.studentAmount.student.checked = true;
            } else {
                this.studentAmount.student.checked = false;
            }
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
