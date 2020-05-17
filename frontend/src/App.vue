<template>
  <div id="app">
    <component :is="layout">
      <router-view />
    </component>
  </div>
</template>

<script>

import DefaultLayout from '@/layouts/Default'
import NoSidebarLayout from '@/layouts/NoSidebar'

export default {
  components: {DefaultLayout, NoSidebarLayout},
  computed: {
    layout() {
      return (this.$route.meta.layout || 'default') + '-layout'
    }
  }
}
</script>

<style lang="scss">
@import 'assets/css/main.scss';
</style>
