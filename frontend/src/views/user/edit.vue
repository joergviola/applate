<template>
  <editor title="Benutzer" type="users">
    <template v-slot:default="$">
      <v-text-field v-model="$.item.name" label="Name"></v-text-field>
      <v-text-field v-model="$.item.email" label="E-Mail"></v-text-field>
      <v-text-field v-model="$.item.password" label="Password" type="password"></v-text-field>
      <v-select :items="roles" v-model="$.item.role_id" label="Rolle" item-value="id" item-text="name"></v-select>
      <div v-for="file in files">
        <a :href="file.url">{{file.original}}</a> {{file}}
      </div>
      <input type="file" name="avatar" multiple="multiple" @change="$.files['avatar/logo'] = $event.target.files">
    </template>
  </editor>
</template>

<script>

  import Editor from "../../components/Editor";
  import api from "../../lib/api"

  export default {
    components: {Editor},
    data: () => ({
      roles: [],
      files: [],
    }),
    created () {
      if (this.$route.params.id!='new') {
        api.find('role', {}).then(roles => this.roles = roles)
        api.getDocs('users', this.$route.params.id).then(files => this.files=files)
      }
    },
  }
</script>