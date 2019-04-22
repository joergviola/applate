<template>
  <div class="elevation-10">
    <v-toolbar flat color="white">
        <router-link :to="{ name: type+'-list'}"><v-icon>arrow_back</v-icon></router-link>
        <v-spacer></v-spacer>
      <v-toolbar-title>{{title}}</v-toolbar-title>
      <v-spacer></v-spacer>
    </v-toolbar>
      <v-card>
          <v-card-text>
              <slot v-bind:item="item"></slot>
          </v-card-text>
          <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="default" :to="{ name: type+'-list'}">Zurück</v-btn>
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
      'cfg': {type: Object},
      'id-column': {type: String, default: 'id'},
    },

    data: () => ({
      item: {},
    }),

    created () {
      if (this.$route.params.id!='new') {
        api.get(this.type, this.$route.params.id).then(item => this.item = item)
      }
    },

    methods: {
      save() {}
    }
  }
</script>