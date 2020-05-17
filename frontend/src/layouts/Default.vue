<template>
    <el-container>
        <el-header  style="border-bottom: 1px solid #DDDDDD">
            <app-header />
        </el-header>
        <el-container>
            <el-aside width="150px">
                <app-sidebar />
            </el-aside>
            <el-main>
                <transition name="content">
                    <router-view ></router-view>
                </transition>
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
import Header from '@/components/layout/Header'
import Sidebar from '@/components/layout/Sidebar'

export default {
  name: 'default-layout',
  components: {
    'app-header': Header,
    'app-sidebar': Sidebar
  }
}</script>
