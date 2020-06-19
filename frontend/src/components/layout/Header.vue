<template>
    <div style="display: flex; justify-content: flex-start;">
        <div style="width: 150px" >
            <img height="50px" style="margin: 5px" src="@/assets/img/logo.png" />
        </div>
        <div  style="flex-grow: 1; display: flex; justify-content: space-between; align-items: stretch;">
            <el-breadcrumb separator-class="el-icon-arrow-right" style="height: 60px; line-height: 60px">
                <el-breadcrumb-item 
                    v-for="(m,i) in $route.matched"
                    :to="breadcrumbPath(i)"
                    :key="i"
                >{{m.name}}</el-breadcrumb-item>
            </el-breadcrumb>            
            <div v-if="user" style="text-align: right;">
                <avatar :user="user" cls="header-avatar"/>
                <el-dropdown style="height: 60px; line-height: 60px; margin-left: auto;margin-right: 25px">
                    <span class="el-dropdown-link text-right">
                        {{user.name}}
                        <i class="el-icon-arrow-down el-icon--right"></i>
                    </span>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item><a @click.prevent="profile">Profile</a></el-dropdown-item>
                        <el-dropdown-item><a @click.prevent="logout">Logout</a></el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
            </div>
        </div>
    </div>
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
        },
        profile() {
            this.$router.push(`/users/${this.user.id}/detail`)
        },
        breadcrumbPath(index) {
            const route = this.$route.matched[index]
            let tmpl = route.path
            if (route.redirect) {
                if (route.redirect[0] == '/') tmpl = route.redirect
                else tmpl += '/' + route.redirect
            }
            const params = this.$route.params

            return { path: this.replace(tmpl, params) }
        },
        replace(tmpl, params) {
            for (let key in params) {
                tmpl = tmpl.replace(':'+key, params[key])
            }
            return tmpl
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