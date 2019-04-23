<template>
  <div class="elevation-10">
    <v-toolbar flat color="white">
      <v-toolbar-title>{{title}}</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-text-field
              v-model="search"
              append-icon="search"
              label="Search"
              single-line
              hide-details
      ></v-text-field>
      <v-spacer></v-spacer>
          <v-fab-transition>
          <v-btn :to="{name:type+'-edit', params: {id: 'new'}}"
                 fab
                 color="primary"
                 dark
                 class="mb-1"
                 small
                 absolute
                 bottom
                 right
          >
          <v-icon>add</v-icon>
          </v-btn>
          </v-fab-transition>
    </v-toolbar>
    <v-data-table
            :headers="cfg.columns"
            :items="items"
            :search="search"
    >
      <template v-slot:items="props">
        <td v-for="col in cfg.columns">{{ prop(props.item, col.value) }}</td>
        <td >
          <router-link :to="{ name: type+'-edit', params: { id: props.item[idColumn] }}"><v-icon
                  small
                  class="mr-2"
          >
            keyboard_arrow_right
          </v-icon></router-link>
        </td>
      </template>
      <template v-slot:no-data>
        <v-btn color="primary" >Reset</v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>

  import api from "../lib/api"
  import get from "lodash.get"

  export default {
    props: {
      'title': {type: String},
      'type': {type: String},
      'cfg': {type: Object},
      'id-column': {type: String, default: 'id'},
    },

    data: () => ({
      search: '',
      items: [],
    }),

    created () {
      api.find(this.type, this.cfg.query).then(items => this.items = items)
    },

    methods: {
      prop(obj, name) {
        return get(obj,name);
      }
    },
  }
</script>