<template>
  <tr>
    <td>
      <input type="checkbox" v-model="student.checked" />
    </td>
    <th scope="row">{{student.user_id}}</th>
    <td>{{student.user.last_name}}</td>
    <td>{{student.user.first_name}}</td>
    <td>{{getGroupName(student.user.group_id)}}</td>
    <td>
      <input type="" name="" class="form-control" :value="Math.round((student.amount + Number.EPSILON) * 100) / 100" />
    </td>
    <td @click="removeStudent();" ><i class="fas fa-user-minus" ></i></td>
  </tr>
</template>

<script>
export default {
  mounted() {
    console.log("Component mounted: Student");
  },
  props: ["student"],
  data: function() {
    return {
      
    };
  },
  methods:{
    removeStudent: function(){
      console.log("deleting student with id " + this.student.user_id);
      this.$emit('removeStudent', this.student.user_id);
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
