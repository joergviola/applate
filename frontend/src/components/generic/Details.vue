<template>
  <div>
    <el-row :gutter="40">
      <el-col :xs="24" :md="image ? 12 : 24">
        <el-form ref="postForm" v-loading="loading" :model="item" label-position="left" label-width="120px" >
          <fields :item="item" :fields="fields" :readonly="readonly" @docs-added="docsAdded" @docs-removed="docsRemoved"/>
        </el-form>
        <el-row type="flex" >
          <el-col :span="24" class="text-right">
            <el-button type="secondary" @click="$router.go(-1)">
              Cancel
            </el-button>
            <el-button v-for="(button, i) in buttons" :key="i" type="danger" @click="click(button)">
              {{ button.label }}
            </el-button>
            <el-button type="primary" @click="save">
              Save
            </el-button>
          </el-col>
        </el-row>
      </el-col>
      <el-col v-if="image" :xs="24" :md="12">
        <img width="100%" :src="image">
      </el-col>
    </el-row>
  </div>
</template>

<script>
import Fields from './Fields'
import api from '../../api'

export default {
  name: 'GenericDetails',
  components: { Fields },
  props: ['type', 'id', 'fields', 'buttons', 'with', 'template', 'image', 'reload'],
  data() {
    return {
      item: this.template || {},
      uploadDocs: {},
      loading: false
    }
  },
  computed: {
    readonly() {
      const rights = api.user().role.rights
        .filter(right => right.tables=='*' || right.tables.search(this.type)!=-1)
        .filter(right => right.actions.indexOf('U')!=-1)
      return rights.length==0
    }
  },
  async created() {
    if (this.id !== 'new') {
      await this.load()
      this.$emit('update', Object.assign({}, this.item))
    }
  },
  methods: {
    async load() {
      this.loading = true
      const items = await api.find(this.type, {
        and: [{ id: this.id }],
        with: this.with
      })
      this.item = items[0]
      this.loading = false
    },
    async click(button) {
      button.action(this.item)
      if (button.andSave) this.save()
    },
    async createNewToOnes() {
      const newToOnes = this.fields
        .filter(f => f.type === 'to-one' && typeof this.item[f.name] === 'string')
      // Don't use foreach here - async!
      for (let i = 0; i < newToOnes.length; i++) {
        const f = newToOnes[i]
        const data = f.create ? f.create(this.item[f.name]) : { name: this.item[f.name] }
        const { id } = await api.create(f.ref, data)
        if (f.input) f.input(id)
        this.item[f.name] = id
      }
    },
    docsAdded(docs) {
      this.uploadDocs[docs.path] = {add:true, docs:docs.files}
    },
    docsRemoved(docs) {
      this.uploadDocs[docs.path] = {remove:true, docs:docs.files}
    },
    async save() {
      this.loading = true
      try {
        await this.createNewToOnes()
        if (this.item.id) {
          await api.update(this.type, this.item.id, this.item)
        } else {
          const result = await api.create(this.type, this.item)
          this.item.id = result.id
        }
        const upload = Object.keys(this.uploadDocs)
          .filter(key => this.uploadDocs[key].add)
        if (upload.length>0) {
          const data = {}
          upload.forEach(key => data[key] = this.uploadDocs[key].docs)
          await api.createDocs(this.type, this.item.id, data)
        }
        const remove = Object.keys(this.uploadDocs)
          .filter(key => this.uploadDocs[key].remove)
          .map(key => this.uploadDocs[key].docs)
          .flat()
          .map(doc => doc.id)
        if (remove.length>0) {
          await api.removeDocs(this.type, this.item.id, remove.join(','))
        }
        if (this.reload) {
          this.load()
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error',
          duration: 5000
        })
      }
      this.loading = false
      this.$emit('update', Object.assign({}, this.item))
    },
  }
}
</script>

<style lang="scss" scoped>
</style>
