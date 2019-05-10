<template>
  <v-data-table
          :headers="headers"
          :items="items"
  >
    <template v-slot:items="props">
      <td >{{ props.item.created_at }}</td>
      <td >{{ props.item.user.name }}</td>
      <td >{{ operations[props.item.operation] }}</td>
      <td v-for="key in props.item.content">{{key}}</td>
      <td ><a v-on:click="restore(props.item)">Aktivieren</a></td>
    </template>
    <template v-slot:no-data>
      No data found
    </template>
  </v-data-table>
</template>

<script>

  import DataTable from "../../components/DataTable";
  import api from "../../lib/api"

  export default {
    components: {DataTable},
    created () {
      api.log(this.$route.params.type, this.$route.params.id).then(items => {
        this.items = items
        if (items.length>0) {
          Object.keys(items[0].content).forEach(k => this.headers.push({text: k, value: k}));
        }
        this.headers.push({text: "", value: ''})
      })
    },
    data: function () {
      return {
        operations : {
          "C" : "Angelegt",
          "U" : "Geändert",
          "D" : "Gelöscht",
        },

      headers: [
          { text: 'Zeitpunkt', value: 'name' },
          { text: 'Von', value: 'email' },
          { text: 'Operation', value: 'role.name' },
      ],
      items: [],
      }
    },
    methods: {
      restore: function(item) {
        api.restore(this.$route.params.type, this.$route.params.id, item.id);
      }
    }
  }
</script>