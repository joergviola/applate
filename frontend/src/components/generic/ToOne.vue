<template>
  <div>
    <el-select v-loading="loading" :value="value" value-key="id" allow-create filterable default-first-option placeholder="Search..." @change="i => $emit('input', i)">
      <el-option v-for="i in items" :key="i.id" :label="i[display]" :value="i.id" />
      <template slot="append">...</template>
    </el-select>
    <router-link v-if="typeof value === 'number' && link " :to="link">...</router-link>
  </div>
</template>

<script>
import Fields from './Fields'
import api from '../../api'

export default {
  name: 'GenericToOne',
  components: { Fields },
  props: ['type', 'value', 'display', 'query', 'link'],
  data() {
    return {
      items: [],
      loading: false
    }
  },
  async created() {
    this.load()
  },
  watch: {
    query() {
      this.load()
    }
  },
  methods: {
    async load() {
      this.loading = true
      this.items = await api.find(this.type, {
        and: this.query || {}
      })
      const selected = this.items.find(i => i.id === this.value)
      if (!selected) this.$emit('input', null)
      this.loading = false
    }
  }
}
</script>

<style lang="scss" scoped>
</style>
