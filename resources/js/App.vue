<template>
    <div id="app">
        <Header></Header>
    </div>
</template>

<script>
    import Header from "./components/layouts/Header";

    export default {
        name: 'base-app', 
        components: {
            'Header': Header
        }
    }
</script>
