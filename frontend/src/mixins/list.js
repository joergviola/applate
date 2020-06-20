
import api from '@/api'
import Sortable from 'sortablejs'

export default {
  data() {
    return {
      list: [],
      loading: true,
    }
  },
  computed: {
    meta() {
      const result = Object.assign({}, this.with)
      for (var key in result) {
        result[key].ignore = true
      }
      return result
    },
    rights() {
      return api.user().role.rights
        .filter(right => right.tables=='*' || right.tables.search(this.type)!=-1)
    },
    readonly() {
      return !this.userCan('U')
    }
  },
  watch: {
    query() {
      this.getList()
    },
    template() {
      this.getList()
    },
  },
  created() {
    this.getList()
  },
  methods: {
    addNew() {
      if (this.createBy == 'row' || this.createBy==null) {
        const item = Object.assign({}, this.template)
        item._meta = this.meta
        if (this.sort) {
          item[this.sort] = this.list.length+1
        }
        this.list.push(item)
      }
    },
    async getList() {
      this.loading = true
      const query =  {
        and: this.query || this.template,
        with: this.with
      }
      if (this.with) query.with = this.with
      if (this.order) query.order = this.order
      if (this.sort) {
        query.order = {}
        query.order[this.sort] = 'ASC'
      }
      try {
        this.list = await api.find(this.type, query)
        this.addNew()
        if (this.sort) {
          this.$nextTick(() => {
            this.setSort()
          })
        }
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error',
          duration: 15000
        })
      }
      this.loading = false
    },
    setSort() {
      const el = this.$refs.theTable.$el.querySelectorAll('.el-table__body-wrapper > table > tbody')[0]
      this.sortable = Sortable.create(el, {
        ghostClass: 'sortable-ghost', // Class name for the drop placeholder,
        setData: function(dataTransfer) {
          // to avoid Firefox bug
          // Detail see : https://github.com/RubaXa/Sortable/issues/1012
          dataTransfer.setData('Text', '')
        },
        onEnd: async evt => {
          const targetRow = this.list.splice(evt.oldIndex, 1)[0]
          this.list.splice(evt.newIndex, 0, targetRow)
          await this.updateSort()
        }
      })
    },
    async updateSort() {
      const data = {}
      this.list.forEach((item, i) => {
        if (item.id) {
          data[item.id] = {}
          data[item.id][this.sort] = i+1
          item[this.sort] = i+1
        }
      })
      api.updateBulk(this.type, data)
    },
    async save(row, attr) {
      if (!row.id) {
        await this.create(row, false)
      } else {
        try {
          const data = {}
          data[attr] = row[attr]
          await api.update(this.type, row.id, data)
        } catch (error) {
          this.$notify({
            title: 'Error',
            message: error.message,
            type: 'error',
            duration: 15000
          })
        }
        }
    },
    async create(row, showError=true) {
      try {
        const result = await api.create(this.type, row)
        row.id = result.id
        this.addNew()
      } catch (error) {
        if (showError) {
          this.$notify({
            title: 'Error',
            message: error.message,
            type: 'error',
            duration: 15000
          })
        }
      }
    },
    async remove(row) {
      try {
        try {
          await this.$confirm('Are you sure?', 'Warning', {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning'
          })
        } catch (cancel) {
          return
        }
        await api.delete(this.type, row.id)
        this.getList()
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error',
          duration: 15000
        })
      }
    },
    async onEnter(row, column, index) {
      if (!row.id) {
        await this.create(row)
      }
      const key = `field-${index + 1}-0`
      let ref = this.$refs[key]
      if (Array.isArray(ref)) ref = ref[0]
      this.$nextTick(() => {
        ref.focus()
      })
    },
    onArrow(column, index, dir) {
      if (0 <= index + dir && index + dir < this.list.length) {
        const key = `field-${index + dir}-${column}`
        let ref = this.$refs[key]
        if (Array.isArray(ref)) ref = ref[0]
        this.$nextTick(() => {
          ref.focus()
        })
      }
    },
    userCan(action) {
      const rights = this.rights.filter(right => right.actions.indexOf(action)!=-1)
      return rights.length!=0
    }
  },
}
