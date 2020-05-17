<template>
    <el-row type="flex" justify="space-between">
        <el-col :span="5">
            <img height="50px" src="@/assets/img/logo.png" />
        </el-col>
        <el-col :span="19">
            <el-breadcrumb separator-class="el-icon-arrow-right" style="height: 60px; line-height: 60px">
                <el-breadcrumb-item 
                    v-for="(m,i) in $route.matched"
                    :to="{ path: m.path }"
                    :key="i"
                >{{m.name}}</el-breadcrumb-item>
            </el-breadcrumb>            
        </el-col>
        <el-col :span="3" >
            <el-dropdown style="height: 60px; line-height: 60px">
            <span class="el-dropdown-link text-right">
                {{user.name}}
                <i class="el-icon-arrow-down el-icon--right"></i>
            </span>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item<a @click.prevent="logout">Logout</a></el-dropdown-item>
            </el-dropdown-menu>
            </el-dropdown>
        </el-col>
    </el-row>
</template>

<script>

import api from '@/api'

export default {
    name: "Header",
    data() {
        return {
            user: api.user()
        }
    },
    methods: {
        async logout() {
            await api.logout()
            this.$router.push("/login")
        }
    }
}
</script>