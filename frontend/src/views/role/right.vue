<template>
  <editor title="Recht" type="right" back="role-edit">
    <template v-slot:default="$">
      <v-text-field v-model="$.item.tables" label="Tables"></v-text-field>
      <v-text-field v-model="$.item.columns" label="Columns"></v-text-field>
      <v-text-field v-model="$.item.where" label="Where"></v-text-field>
      <v-text-field v-model="$.item.actions" label="Actions"></v-text-field>
    </template>
  </editor>
</template>

<script>

  import Editor from "../../components/Editor";

  export default {
    components: {Editor},
    data: () => ({
    }),
    created () {
    },

  }
</script>