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
                        <td><input type="text" name="user[]" id="user_autocomplete" class="form-control typeahead" placeholder="Name oder ID" @focus="autocomplete()"></td>
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
                var input = $("#user_autocomplete");
                var self = this;
                axios.get('/api/getStudent', {
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        params: {
                            user: input.val()
                        }
                    }).then(response =>{
                        console.log(response.data);
                        self.$parent.students.push(response.data);
                    });

                    

                
            }
        },
    }
</script>
