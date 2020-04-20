<template>
  <tr>
    <td>
      <input type="checkbox" v-model="studentAmount.student.checked" />
    </td>
    <th scope="row">{{studentAmount.student.id}}</th>
    <td>{{studentAmount.student.last_name}}</td>
    <td>{{studentAmount.student.first_name}}</td>
    <td>{{getGroupName(studentAmount.student.group_id)}}</td>
    <td>
      <input type="" name="" class="form-control" :value="Math.round((studentAmount.amount + Number.EPSILON) * 100) / 100" />
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
    return {
      
    };
  },
  methods:{
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
