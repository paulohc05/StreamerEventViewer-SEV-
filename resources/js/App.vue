<template>
    <div id="app">
        <Header></Header>

        <main role="main" class="container text-center">
            <router-view></router-view>
        </main>
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
