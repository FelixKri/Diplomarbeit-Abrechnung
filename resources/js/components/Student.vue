<template>
    <tr>
        <td>
            <input type="checkbox" v-model="student.checked">
        </td>
        <td>{{ getGroupName(student.group_id) }}</td>
        <td>{{ student.last_name }}</td>
        <td>{{ student.first_name }}</td>
        <td>
            <input type="number" name="amount[]" id="" class="form-control text-right" v-model="student.amount">
        </td>
        <td>
            <input type="text" name="annotation[]" id="" class="form-control" v-model="student.annotation">
        </td>
        <td @click="removeStudent();" ><i class="fas fa-user-minus" ></i></td>
    </tr>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted: Student')
        },
        props: ["student"],
        data: function () {
            return {
                
            }
        },
        methods:{
            removeStudent: function(){
                console.log("deleting student with id " + this.student.id);
                this.$emit('removeStudent', this.student.id);
            },
            editStudent: function(){
                //Unsicher ob man das wirklich braucht 
            },
            getGroupName: function(groupId)
            {
                //Really stupid solution, but necessary
                //if we put group: this.$parent.group ... in data
                //it will copy once on creation
                //Thus will not have the groups in it
                var prescribingForm = this.$parent.$parent;

                for(var i = 0;i < prescribingForm.groupLength;i++)
                {
                    if(prescribingForm.groups[i]["id"] == groupId)
                        return prescribingForm.groups[i]["name"];
                }
                //Should not get here, pretty much an error
                return "Unbekannt";
            }
        }
    }
</script>
