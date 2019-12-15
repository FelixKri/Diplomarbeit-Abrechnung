<template>
  <tr>
    <td>
      <input type="checkbox" v-model="student.checked" />
    </td>
    <th scope="row">{{student.id}}</th>
    <td>{{student.last_name}}</td>
    <td>{{student.first_name}}</td>
    <td>{{this.$parent.groups[student.group_id]["name"]}}</td>
    <td>
      <input type="number" name="amount[]" id class="form-control" v-model="student.amount" />
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
      console.log("deleting student with id " + this.student.id);
      this.$emit('removeStudent', this.student.id);
    },
  }
};
</script>
