<template>
    <div
        style="position: relative;"
        class="tab-pane fade show"
        :id="'nav-overview'"
        role="tabpanel"
        :aria-labelledby="'nav-overview-tab'"
    >
        <h2 style="">Übersicht</h2>
        
        
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">X</th>
                    <th scope="col">ID</th>
                    <th scope="col">Nachname</th>
                    <th scope="col">Vorname</th>
                    <th scope="col">Klasse</th>
                    <th scope="col">Abgerechneter Betrag</th>
                    <th scope="col">Vorgeschriebener Betrag</th>
                    <th scope="col">Betrag</th>
                </tr>
            </thead>
            <tbody>
                <student-overview-invoice
                    
                ></student-overview-invoice>
            </tbody>
        </table>

    </div>
</template>

<script>
export default {
    data: function() {
        return {
            prescribing: null,
        };
    },
};
</script>

<style></style>
