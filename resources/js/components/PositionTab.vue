<template>
    <a class="nav-item nav-link active" :id="'nav-' + home + '-tab'" data-toggle="tab" :href="'#nav-' + home" role="tab" :aria-controls="'nav-' + home" aria-selected="true">{{name}}</a>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        props: [
            "name"
        ]
    }
</script>
