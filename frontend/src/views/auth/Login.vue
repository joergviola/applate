<template>
  <div class="about">
    <el-card v-loading="loading">
      <h1>Login</h1>
        <el-form>
          <el-form-item label="E-Mail">
            <el-input v-model="email" />
          </el-form-item>
          <el-form-item label="Password">
            <el-input v-model="password" show-password />
          </el-form-item>
          <el-button type="primary" @click="login">Login</el-button>
        </el-form>
    </el-card>
  </div>
</template>

<script>

import api from '@/api'

export default {
  name: 'Login',
  data() {
    return {
      email :null,
      password: null,
      loading: false
    }
  },
  methods: {
    async login() {
      try {
        this.loading = true
        const result = await api.login(this.email, this.password)
        this.$router.push("/")
      } catch (error) {
        this.$notify.error({
          title: 'Could not login',
          message: error.message
        });
        this.$router.push("/")
      }
      this.loading = false
    }
  }
}
</script>