<template>
    <section class="section text-center">
        <h1>Streaming Page</h1>

        <!-- Add a placeholder for the Twitch embed -->
        <div id="twitch-embed"></div>

        <br />

        <div class="container" v-if="events.length > 0">
            <div class="row">
                <div class="col-md-12">
                    <h5 class="border-bottom border-gray pb-2 mb-0">Recent updates</h5>

                    <div class="media text-muted pt-3 text-left" v-for="(event, index) in events" :key="index">
                        <img class="bd-placeholder-img mr-2 rounded" :src="channel_thumb" :alt="channel" width="32" height="32">
                        <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                            <strong class="d-block text-gray-dark">@{{ channel }}</strong>
                            {{ event }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <hr />

        <div class="container">
            <div class="row">
                <div v-for="(item, index) in streamers" :key="index" class="col-md-3">
                    <div class="card">
                        <img :src="item.thumbnail_url" class="card-img-top" :alt="item.title">
                        <div class="card-body">
                            <h5 class="card-title">{{ item.title }}</h5>
                            <p class="card-text">
                                Username: {{ item.user_name }} <br />
                                Type: {{ item.type }} <br />
                                Language: {{ item.language }} <br />
                                Viewers: {{ item.viewer_count }} <br />
                            </p>
                            <a href="javascript:void(0)" class="btn btn-primary" v-on:click="embedVideo(item.user_id, item.user_name)">Stream</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
  import axios from 'axios';
  import Pusher from 'pusher-js';

  export default {
      name: 'dashboard-page', 
      data() {
          return {
              token: '', 
              channel: '', 
              channel_thumb: '', 
              streamers: [], 
              events: []
          }
      }, 
      created() {
        var vm = this;

        vm.token = vm.getAccessToken();
        vm.streamers = vm.listOfStreamers();
      }, 
      mounted() {
        var vm = this;

        var twitchScript = document.createElement('script');
        twitchScript.type = "text/javascript";
        twitchScript.src = "https://embed.twitch.tv/embed/v1.js";
        document.head.appendChild(twitchScript);
      }, 
      methods: {
        getAccessToken() {
            var vm = this;

            var token = null;
            if (vm.$route.hash.includes("access_token")) {
                var pattern = new RegExp(/access_token=(.*?)&/);
                var regex=vm.$route.hash.match(pattern);

                token = regex[1] ? regex[1] : token;
            }
            
            return token;
        }, 
        listOfStreamers() {
            var vm = this;

            axios.get('./api/streamers')
            .then(response => {
                vm.streamers = (response.data).map(function(data) {
                    var temp = Object.assign({}, data);
                    var thumbnail_url = temp.thumbnail_url;
                    temp.thumbnail_url = thumbnail_url.replace("{width}x{height}", "280x180");
                    return temp;
                });
            })
            .catch(error => {
                console.log(error);
            });
        }, 
        embedVideo(user_id, user_name) {
            var vm = this;

            vm.channel = user_name;
            document.getElementById("twitch-embed").innerHTML = "";

            var twitchEmbed = new Twitch.Embed("twitch-embed", {
                width: 854,
                height: 480,
                channel: vm.channel
            });

            axios.get('./api/streamer/' + user_id + '/' + vm.token)
            .then(response => {
                vm.events = [];

                var responseData = response.data;
                vm.channel_thumb = responseData.profile_image_url;

                vm.events.push('STREAMING: ' + responseData.title);

                var pusher = new Pusher('2ad17b749b0c399d9336', {
                    cluster: 'us3',
                });
                var channel = pusher.subscribe(vm.channel);
                channel.bind('streamer_event', function(data) {
                    vm.events.unshift(data.message);
                });
            })
            .catch(e => {
                console.log(e);
            });
        }
      }
  }
</script>