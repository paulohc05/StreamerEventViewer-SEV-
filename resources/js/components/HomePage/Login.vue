<template>
    <section class="section text-center">
        <form class="form-signin">
            <img class="mb-4" src="img/twitch-logo.jpg" alt="" width="474" height="237">
            <br/>
            <button class="btn btn-lg btn-primary btn-block" type="button" v-on:click="proceed">Log in with Twitch</button>
        </form>
    </section>
</template>

<script>
    export default {
        name: 'login-page', 
        data () {
            return {
                loading: false
            }
        }, 
        methods: {
            proceed() {
                var vm = this;

                // Get the authentication URL and redirect the User.
                fetch('./api/auth').then(function (response) {
                    response.text().then(function (url) {
                        vm.loading = false;
                        window.location.href = url;
                    });
                }).catch(function (error) {
                    console.log(error);
                });
            }
        }
    }
</script>