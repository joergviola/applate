<template>
    <v-app id="inspire">
        <v-content>
            <v-container fluid fill-height>
                <v-layout align-center justify-center>
                    <v-flex xs12 sm8 md4>
                        <v-card class="elevation-12">
                            <v-toolbar dark color="primary">
                                <v-toolbar-title>Login form</v-toolbar-title>
                                <v-spacer></v-spacer>
                            </v-toolbar>
                            <v-card-text>
                                <v-form>
                                    <v-text-field prepend-icon="person" name="login" label="Login" type="text"
                                                  v-model="email"></v-text-field>
                                    <v-text-field id="password" prepend-icon="lock" name="password" label="Password"
                                                  type="password" v-model="password"></v-text-field>
                                </v-form>
                                <v-alert type="error" :value="message" transition="scale-transition">{{message}}</v-alert>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="primary" @click="login">Login</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-content>
    </v-app>
</template>

<script>
  import api from "@/lib/api.js"

  export default {
    data: () => ({
      email: "",
      password: "",
      message: null,
    }),
    methods: {
      login() {
        this.message = null;
        api.login(this.email, this.password)
          .then(user => this.$router.push({path: "/"}))
          .catch(e => this.message = e.message)
      }
    }
  }
</script>