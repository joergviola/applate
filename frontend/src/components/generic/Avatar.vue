<template>
  <span :class="cls">
    <el-avatar v-if="avatar" :size="40" :src="avatar.url"></el-avatar>
    <el-avatar v-if="!avatar" :size="40" >{{avatarText}}</el-avatar>
  </span>
</template>

<script>

import api from '@/api'

export default {
    name: "AvatarPicture",
    props: ['user', 'cls'],
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
}
</script>
