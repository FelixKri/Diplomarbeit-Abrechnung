<template>
    <div id="addClass" class="modal fade" role="dialog">
        <div class="modal-dialog">
    
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Klasse hinzufügen</h4>
                </div>
                <div class="modal-body">
                    <ul>
                        <li v-bind:key="className" v-for="className in data.classes">{{ className }} <button type="button" class="btn btn-danger btn-sm" style="display: inline; width: 100px; cursor: pointer;" @click="removeClass(className);">entfernen</button></li>
                    </ul>
                        <table>
                            <tr>
                                <td><input type="text" id="classes_autocomplete" class="form-control typeahead" placeholder="Klassenname" @focus="autocomplete()"></td>
                                <td><button type="button" class="btn btn-primary" @click="addClassToDOM">+</button></td>
                            </tr>
                        </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal" @click="addStudents">Hinzufügen</button>
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
        data: function () {
            return {
                data: this.$parent
            }
        },
        methods: {
            autocomplete: function() {

                console.log("autocomplete function launched");
                $( "#classes_autocomplete" ).autocomplete({
                    source: "http://localhost:8000/class/autocomplete/"
                });
            },

            addClassToDOM: function(){
                var input = $("#classes_autocomplete");
                this.$parent.classes.push(input.val());
                input.val("");
            },

            addStudents: function(){
                var self = this;

                this.$parent.classes.forEach(className => {
                    axios.get('/api/getStudentsFromClass', {
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        params: {
                            name: className,
                        }
                    })
                    .then(response => {
                        console.log(response.data);

                        response.data.forEach(student => {
                        self.$parent.students.push(student);
                        });
                        
                        console.log(self.$parent.students); 
                    })
                    .catch(error => {
                        console.log(error);
                    });
                });
                
            },
            removeClass: function(cl){
                this.data.classes = this.data.classes.filter(el => el !== cl);
            }
        },

    }
    
</script>

<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }    
</style>
