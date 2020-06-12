<template>
    <el-row type="flex" justify="space-between">
        <el-col :span="5">
            <img height="50px" src="@/assets/img/logo.png" />
        </el-col>
        <el-col :span="16">
            <el-breadcrumb separator-class="el-icon-arrow-right" style="height: 60px; line-height: 60px">
                <el-breadcrumb-item 
                    v-for="(m,i) in $route.matched"
                    :to="{ path: m.path }"
                    :key="i"
                >{{m.name}}</el-breadcrumb-item>
            </el-breadcrumb>            
        </el-col>
        <el-col :span="6" align="right">
            <avatar :user="user" cls="header-avatar"/>
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
import Avatar from '@/components/generic/Avatar'
import api from '@/api'

export default {
    name: "Header",
    components: {Avatar},
    data() {
        return {
            user: api.user()
        }
    },
    computed: {
        avatar() {
            const docs = this.user.documents.filter(doc => doc.path=='avatar')
            if (docs.length>0) return docs[0]
            else return null
        },
        avatarText() {
            return this.user.name
                .split(' ')
                .map(name => name.substring(0, 1).toUpperCase())
                .join('')
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

<style lang="scss" scoped>
.el-dropdown-menu {
    padding: 10px 20px;
}

header .header-avatar {
    margin-top: 10px;
    margin-right: 10px;
    vertical-align: -70%;
}
</style>