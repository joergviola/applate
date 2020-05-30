<template>
   <div style="display: flex; flex-direction: column; height: 100%;">
      <router-menu />
      <router-menu :bottom="true" />
   </div>
</template>

<script>
import RouterMenu from './RouterMenu'

export default {
    name: "Sidebar",
    components: {RouterMenu},
}
</script>