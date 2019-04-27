<template>
    <section class="section text-center">
        <h1>Streaming Page</h1>

        <form v-on:submit.prevent="searchWatch()">
            <div class="container">
                <div class="row">
                    <div class="input-group mb-12">
                        <input type="text" v-model="searchChannel" class="form-control" placeholder="Search by username">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">Search and Watch</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <br />

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
              searchChannel: '', 
              channel_thumb: '', 
              streamers: [], 
              events: []
          }
      }, 
      created() {
        var vm = this;

        // Getting the Access Token from the URI before DOM is rendered.
        vm.token = vm.getAccessToken();

        // Getting the Streamers Data as JSON format.
        vm.streamers = vm.listOfStreamers();
      }, 
      mounted() {
        var vm = this;

        // Adding the Twitch embed script as it is required to stream the video.
        var twitchScript = document.createElement('script');
        twitchScript.type = "text/javascript";
        twitchScript.src = "https://embed.twitch.tv/embed/v1.js";
        document.head.appendChild(twitchScript);
      }, 
      methods: {
        getAccessToken() {
            var vm = this;

            // Extract the Access Token from the URI using Regular Expression
            var token = null;
            if (vm.$route.hash.includes("access_token")) {
                var pattern = new RegExp(/access_token=(.*?)&/);
                var regex=vm.$route.hash.match(pattern);

                token = regex[1] ? regex[1] : token;
            }

            // If token is null, redirect to login page
            if (null === token) {
                window.location.href = './#/login';
            }
            
            return token;
        }, 
        listOfStreamers() {
            var vm = this;

            // Getting Streamers Data with Axios which will be listed on the Dashboard page.
            axios.get('./api/streamers')
            .then(response => {
                // Modify the thumbnail_url to a specific dimension.
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
        searchWatch() {
            var vm = this;

            // Fetching the User Data and embed it if the keyword is matched.
            fetch('./api/channel/' + vm.searchChannel).then(function (response) {
                response.text().then(function (data) {
                    var dataObj = JSON.parse(data);
                    if (Object.keys(dataObj).length === 0) {
                        // If there is no result, will clear the events and the twitch-embed element.
                        vm.events = [];
                        document.getElementById("twitch-embed").innerHTML = "";
                        return false;
                    } else {
                        // If the keyword is found, then will embed the video.
                        vm.embedVideo(dataObj.id, dataObj.display_name);
                    }
                });
            }).catch(function (error) {
                console.log(error);
            });
        }, 
        embedVideo(user_id, user_name) {
            var vm = this;

            // Assign the user_name as the Stream Channel.
            vm.channel = user_name;
            document.getElementById("twitch-embed").innerHTML = "";

            // Reference: https://dev.twitch.tv/docs/embed/everything/
            // Create a Twitch.Embed object that will render within the "twitch-embed" root element.
            var twitchEmbed = new Twitch.Embed("twitch-embed", {
                width: 854,
                height: 480,
                channel: vm.channel
            });

            axios.get('./api/streamer/' + user_id + '/' + vm.token)
            .then(response => {
                // Clear the event array when the user switches to other channel.
                vm.events = [];

                // Add the current channel data into the event.
                // Which will be appeared immediately once user is on the channel.
                var responseData = response.data;
                vm.channel_thumb = responseData.profile_image_url;
                vm.events.push('STREAMING: ' + responseData.title);

                // Open a connection to channel using the KEY and CLUSTER.
                var pusher = new Pusher('2ad17b749b0c399d9336', {
                    cluster: 'us3',
                });

                // Subscribe via channel (which is the user_name)
                var channel = pusher.subscribe(vm.channel);

                // Bind it with the event. In here, I set `streamer_event` as the event name.
                channel.bind('streamer_event', function(data) {
                    vm.events.unshift(data.message);
                });

                console.log(vm.events);
            })
            .catch(e => {
                console.log(e);
            });
        }
      }
  }
</script>