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
                    <form action="/add/class" method="post">
                        <table>
                            <tr>
                                <td><input type="text" name="class[]" id="classes_autocomplete" class="form-control typeahead" placeholder="Klassenname" @focus="autocomplete()"></td>
                                <td><button type="button" class="btn btn-primary">+</button></td>
                            </tr>
                        </table>
                        
                    </form>
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
                $( "#classes_autocomplete" ).autocomplete({
                    source: "http://localhost:8000/class/autocomplete/"
                });
            },
        },
    }
    
</script>

<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }    
</style>
