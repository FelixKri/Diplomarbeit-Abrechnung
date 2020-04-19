<template>
  <tr>
    <td>
      <input type="checkbox" v-model="this.student.checked" />
    </td>
    <th scope="row">{{this.student.id}}</th>
    <td>{{this.student.last_name}}</td>
    <td>{{this.student.first_name}}</td>
    <td>{{getGroupName(this.student.group_id)}}</td>
    <td>
      <input type="" name="" class="form-control" :value="Math.round((this.student.amount + Number.EPSILON) * 100) / 100" />
    </td>
    <td @click="removeStudent();" ><i class="fas fa-user-minus" ></i></td>
  </tr>
</template>

<script>
export default {
  mounted() {
    console.log("Component mounted: StudentOverviewInvoice");
  },
  props: ["student"],
  data: function() {
    return {
      
    };
  },
  methods:{
    removeStudent: function(){
      console.log("deleting student with id " + this.student.id);
      this.$emit('removeStudent', this.student.id);
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
