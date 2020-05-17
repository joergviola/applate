<template>
  <div class="components-container">
    <generic-details type="users" :id="$route.params.id" :fields="fields" />
  </div>
</template>

<script>

import GenericDetails from '@/components/generic/Details'
import api from '@/api'

export default {
  name: 'UserForm',
  components: { GenericDetails },
  data() {
    return {
      fields: [
        { name: 'name', label: 'Name' },
        { name: 'email', label: 'E-Mail' },
        { name: 'phone', label: 'Phone' },
        { name: 'password', label: 'Password', type: 'password' },
        { name: 'role_id', label: 'Role', type: 'to-one', ref: 'role', display: 'name'},
      ],
    }
  },
}
</script>

<style lang="scss" scoped>
</style>
