<template>
    <el-row type="flex" justify="center" style="height: 100%; align-items: center;">
        <el-col :span="12">
            <transition name="content">
                <router-view class="inner"></router-view>
            </transition>
        </el-col>
    </el-row>
</template>

<script>

export default {
  name: 'no-sidebar-layout',
}

</script>
