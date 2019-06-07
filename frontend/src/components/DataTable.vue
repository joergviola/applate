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
          <v-btn :to="toLink('new')"
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
            :headers="headers"
            :items="items"
            :search="search"
    >
      <template v-slot:items="props">
        <td v-for="col in cfg.columns">{{ prop(props.item, col.value) }}</td>
        <td >
          <router-link :to="toLink(props.item[idColumn])"><v-icon
                  small
                  class="mr-2"
          >
            keyboard_arrow_right
          </v-icon></router-link>
        </td>
      </template>
      <template v-slot:no-data>
        No data found
      </template>
    </v-data-table>
  </div>
</template>

<script>

  import api from "../lib/api"
  import get from "lodash.get"
  import clonedeep from "lodash.clonedeep"

  export default {
    props: {
      'title': {type: String},
      'type': {type: String},
      'cfg': {type: Object},
      'to': {type: Object},
      'id-column': {type: String, default: 'id'},
      'action-label': {type: String, default: 'Aktionen'},
    },

    data: () => ({
      search: '',
      items: [],
    }),

    computed: {
      headers() {
        const result = this.cfg.columns.slice();
        result.push({ text: this.actionLabel, value: null, sortable: false });
        return result;
      }
    },

    created () {
      api.find(this.type, this.cfg.query).then(items => this.items = items)
    },

    methods: {
      toLink(id) {
        const link = this.to ? clonedeep(this.to) :  {name:this.type+'-edit', params: {id: 'new'}}
        link.params['id'] = id;
        return link;
      },
      prop(obj, name) {
        let value = get(obj,name);
        const col = this.cfg.columns.find(c => c.value==name);
        if (col.map) {
          value = col.map(value);
        }
        return value;
      }
    },
  }
</script>