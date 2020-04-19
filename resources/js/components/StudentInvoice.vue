<template>
  <tr>
    <td>
      <input type="checkbox" v-model="this.studentAmount.student.checked" />
    </td>
    <th scope="row">{{this.studentAmount.student.id}}</th>
    <td>{{this.studentAmount.student.last_name}}</td>
    <td>{{this.studentAmount.student.first_name}}</td>
    <td>{{getGroupName(this.studentAmount.student.group_id)}}</td>
    <td>
      <input type="" name="" class="form-control" :value="Math.round((this.studentAmount.amount + Number.EPSILON) * 100) / 100" />
    </td>
    <td @click="removeStudent();" ><i class="fas fa-user-minus" ></i></td>
  </tr>
</template>

<script>
export default {
  mounted() {
    console.log("Component mounted: Student");
  },
  props: ["studentAmount"],
  data: function() {
    return {
      
    };
  },
  methods:{
    removeStudent: function(){
      console.log("deleting student with id " + this.studentAmount.student.id);
      this.$emit('removeStudent', this.studentAmount.student.id);
    },
    getGroupName: function(id)
    {

      for(var i = 0;i < this.$parent.groupLength;i++)
      {
        if(this.$parent.groups[i]["id"] == id)
        {
          return this.$parent.groups[i]["name"];
        }
      }

      console.log("Could not find groupId: " + id);
      return "Error";
    }
  }
};
</script>
