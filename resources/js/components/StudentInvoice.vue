<template>
    <tr>
        <td>
            <input
                type="checkbox"
                v-model="this.studentAmount.student.checked"
            />
        </td>
        <td>{{ this.studentAmount.student.last_name }}</td>
        <td>{{ this.studentAmount.student.first_name }}</td>
        <td>{{ getGroupName(this.studentAmount.student.group_id) }}</td>
        <td>
            <input
                type="number"
                name=""
                class="form-control text-right"
                v-model="studentAmount.amount"
            />
        </td>
    </tr>
</template>

<script>
export default {
    mounted() {
        console.log("Component mounted: Student");
    },
    props: ["studentAmount"],
    data: function() {
        return {};
    },
    methods: {
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
