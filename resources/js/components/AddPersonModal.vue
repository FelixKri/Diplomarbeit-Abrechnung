<template>
    <div id="addUser" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Person hinzufügen</h4>
            </div>
            <div class="modal-body">
                <table>
                    <tr>
                        <td><input type="text" name="user[]" id="user_autocomplete" class="form-control typeahead" placeholder="Name oder ID" @focus="autocomplete()" v-model="input"></td>
                        <td><button type="button" class="btn btn-primary" @click="addStudent">+</button></td>
                    </tr>
                </table>        
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
  
    </div>
  </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        methods: {
            autocomplete: function() {
                console.log("autocomplete function launched");
                $( "#user_autocomplete" ).autocomplete({
                    source: "http://localhost:8000/user/autocomplete/"
                });
            },
            addStudent: function(){
                var that = this;
                axios.get('/api/getStudent', {
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    params: {
                        user: that.input
                    }})
                    .then(response =>{
                        console.log(response.data);
                        that.$parent.students.push(response.data);
                    });

                    

                
            }
        },
        data: function () {
            return {
                input: ""
            }
        },
    }
</script>
