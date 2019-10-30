<template>
  <tr>
    <td>
      <input type="checkbox" v-model="student.checked" />
    </td>
    <th scope="row">{{student.id}}</th>
    <td>{{student.last_name}}</td>
    <td>{{student.first_name}}</td>
    <td>{{student.group_id}}</td>
    <td>
      <input type="number" name="amount[]" id class="form-control" v-model="student.amount" />
    </td>
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
      checked: null
    };
  }
};
</script>
