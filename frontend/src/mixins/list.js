
import api from '@/api'
import Sortable from 'sortablejs'

export default {
  data() {
    return {
      list: null,
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
      this.list = await api.find(this.type, query)
      this.addNew()
      if (this.sort) {
        this.$nextTick(() => {
          this.setSort()
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
        onEnd: evt => {
          const targetRow = this.list.splice(evt.oldIndex, 1)[0]
          this.list.splice(evt.newIndex, 0, targetRow)
          this.updateSort()
        }
      })
    },
    async updateSort() {
      const data = {}
      this.list.forEach((item, i) => {
        data[item.id] = {}
        data[item.id][this.sort] = i
      })
      api.updateBulk(this.type, data)
    },
    async save(row, attr) {
      if (!row.id) return
      const data = {}
      data[attr] = row[attr]
      await api.update(this.type, row.id, data)
    },
    async create(row) {
      try {
        const result = await api.create(this.type, row)
        row.id = result.id
        this.addNew()
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error',
          duration: 5000
        })
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
          await api.delete(this.type, row.id)
          this.getList()
        } catch (cancel) {}
      } catch (error) {
        this.$notify({
          title: 'Error',
          message: error.message,
          type: 'error',
          duration: 5000
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

  },
}
