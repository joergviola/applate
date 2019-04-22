<template>
  <editor title="Benutzer" type="user">
    <template v-slot:default="$">
      <v-text-field v-model="$.item.name" label="Name"></v-text-field>
      <v-text-field v-model="$.item.email" label="E-Mail"></v-text-field>
      <v-text-field v-model="$.item.password" label="Password" type="password"></v-text-field>
    </template>
  </editor>
</template>

<script>

  import Editor from "../../components/Editor";

  export default {
    components: {Editor},
    data: () => ({
    }),
  }
</script>