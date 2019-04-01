<template>
    <div class="tab-pane fade" :id="'nav-'+name" role="tabpanel" :aria-labelledby="'nav-' + name + '-tab'">
        <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptatum quam quas totam possimus laudantium natus, ab hic beatae rem ducimus qui consectetur impedit quod quo recusandae, fugiat placeat temporibus ad.</p>
    </div>
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
