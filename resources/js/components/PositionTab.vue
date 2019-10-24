<template>
    <a class="nav-item nav-link" :id="'nav-' + position.name + '-tab'" data-toggle="tab" 
                    :href="'#nav-' + position.name" role="tab" :aria-controls="'nav-' + position.name" 
                    aria-selected="true">
        {{position.name}}
    </a>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted: PositionTab')
        },
        props: [
            "position"
        ]
    }
</script>
