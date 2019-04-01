<template>
    <div class="">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <position-tab></position-tab>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <invoice-position></invoice-position>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
