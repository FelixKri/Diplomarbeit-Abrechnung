<template>
    <tr>
        <th scope="row">
            <input type="number" name="students[]" id="" readonly v-bind:value="data.id">
        </th>
        <td>{{ data.group_id }}</td>
        <td>{{ data.last_name }}</td>
        <td>{{ data.first_name }}</td>
        <td>
            <input type="number" name="amount[]" id="" class="form-control" v-model="amount">
        </td>
        <td>
            <input type="text" name="annotation[]" id="" class="form-control" v-model="annotation">
        </td>
        <td><i class="fas fa-edit" @click="editStudent();"></i></td>
        <td @click="removeStudent();" ><i class="fas fa-user-minus" ></i></td>
    </tr>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        props: ["data"],
        data: function () {
            return {
                amount: 0,
                annotation: "",
            }
        },
        methods:{
            removeStudent: function(){
                console.log("deleting student with id " + this.data.id);
                this.$emit('removeStudent', this.data.id);
            },
            editStudent: function(){

            },
        }
    }
</script>
