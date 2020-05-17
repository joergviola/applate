<template>
   <el-menu
      default-active="2"
      class="el-menu-vertical-demo"
      background-color="#545c64"
      text-color="#fff"
      active-text-color="#ffd04b"
      style="height: 100%">
      <el-menu-item 
        v-for="(route, i) in $router.options.routes" 
        v-if="!route.meta.hidden"
        :key="i" 
        :index="'item-'+i">
          <router-link :to="route.path">
              <i v-if="route.meta.icon" :class="route.meta.icon"></i>
              <span>{{ route.name }}</span>
            </router-link>
      </el-menu-item>
   </el-menu>
</template>

<script>
export default {
    name: "Sidebar"
}
</script>