<template>
  <div class="elevation-10">
    <v-toolbar flat color="white">
        <router-link :to="backLink()"><v-icon>arrow_back</v-icon></router-link>
        <v-spacer></v-spacer>
      <v-toolbar-title>{{title}}</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
      <v-card>
          <v-card-text>
              <slot v-bind:item="item"></slot>
          </v-card-text>
          <v-card-actions>
              <v-btn color="default" :to="history()">Historie</v-btn>
              <v-spacer></v-spacer>
              <v-btn color="default" :to="backLink()">Zurück</v-btn>
              <v-btn color="primary" @click="save">Speichern</v-btn>
          </v-card-actions>

      </v-card>
  </div>
</template>

<script>

  import api from "../lib/api"

  export default {
    props: {
      'title': {type: String},
      'type': {type: String},
      'back': {type: String},
    },

    data: () => ({
      item: {},
    }),

    created () {
      if (this.$route.params.id!='new') {
        api.get(this.type, this.$route.params.id).then(item => this.item = item)
      }
      Object.keys(this.$route.params).forEach(key => {
        if (key=='id') return;
        this.item[key] = this.$route.params[key];
      })
    },

    methods: {
      history() {
        return { name: 'log-list', params: {'type': this.type, id: this.$route.params.id}}
      },
      backLink() {
        return { name: this.back || this.type+'-list'}
      },
      save() {
        if (this.$route.params.id!='new') {
          api.update(this.type, this.$route.params.id, this.item).then(item => this.$router.push(this.backLink()))
        } else {
          api.create(this.type, this.item).then(item => this.$router.push(this.backLink()))
        }
      }
    }
  }
</script>