<template>
  <editor title="Rolle" type="role">
    <template v-slot:default="$">
      <v-text-field v-model="$.item.name" label="Name"></v-text-field>
      <data-table title="Rechte" type="right" :cfg="cfg"
                  :to="{name:'right-edit', params: {role_id: $route.params.id, id: 'new'}}"
      ></data-table>
    </template>
  </editor>
</template>

<script>

  import Editor from "../../components/Editor";
  import DataTable from "../../components/DataTable";

  export default {
    components: {Editor, DataTable},
    data: function() {
      return {
        cfg: {
          columns: [
            { text: 'Tables', value: 'tables' },
            { text: 'Columns', value: 'columns' },
            { text: 'Where', value: 'where' },
            { text: 'Actions', value: 'actions' },
          ],
                  query : {
            and: {
              role_id: this.$route.params.id
            }
          }

        },
      }
    },
    created () {
    },

  }
</script>