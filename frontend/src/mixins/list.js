
import api from '@/api'

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
  created() {
    this.getList()
  },
  methods: {
    addNew() {
      if (this.createBy !== 'button') {
        const item = Object.assign({}, this.template)
        item._meta = this.meta
        this.list.push(item)
      }
    },
    async getList() {
      this.loading = true
      this.list = await api.find(this.type, {
        and: this.query || this.template,
        with: this.with
      })
      this.addNew()
      this.loading = false
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
        ref.focus()
      }
    },

  },
}
