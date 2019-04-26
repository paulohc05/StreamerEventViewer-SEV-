<template>
    <section class="section text-center">
        <h1>Streaming Page</h1>

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
  
  export default {
      name: 'dashboard-page', 
      data() {
          return {
              streamers: []
          }
      }, 
      created() {
        var vm = this;

        vm.streamers = vm.listOfStreamers();
      }, 
      mounted() {
        var vm = this;
      }, 
      methods: {
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

            console.log(user_id + ' ' + user_name);

        }
      }
  }
</script>