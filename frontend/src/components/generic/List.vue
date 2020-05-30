<template>
  <div>
    <div class="filter-container">
      <el-col :span="24" type="flex" align="right">
        <slot name="header"></slot>
        <el-button v-if="createBy=='button'" class="filter-item pull-right" style="margin-right: 10px;" type="primary" icon="el-icon-edit" @click="$router.push(detail + '/new/detail')">
          Add
        </el-button>
      </el-col>
    </div>

    <el-table v-loading="loading" :data="list">

      <el-table-column v-for="(col,i) in columns" :key="i" :label="col.label" :prop="col.name" :minWidth="col.width" sortable>
        <template slot-scope="{row, $index}">
          <el-input
            v-if="editable(row, col) && !col.type"
            class="no-border"
            v-model="row[col.name]"
            :disabled="!editable(row, col)"
            @blur="save(row, col.name)"
            :placeholder="col.placeholder"
            :ref="`field-${$index}-${i}`"
            @keyup.enter.native="onEnter(row, i, $index)"
            @keyup.up.native="onArrow(i, $index, -1)"
            @keyup.down.native="onArrow(i, $index, +1)"
          />
          <span v-if="!editable(row, col) && !col.type" class="input-disabled">{{typeof col.name === 'string' ? _.get(row, col.name) : col.name(row) }}</span>
          <el-select v-if="col.type=='select'" class="no-border" v-model="row[col.name]" @blur="save(row, col.name)"  :placeholder="col.placeholder">
            <el-option v-for="(o, i) in col.options" :key="i" :label="col.display ? _.get(o, col.display) : o" :value="col.id ? _.get(o, col.id) : o" />
          </el-select>
          <el-date-picker
            :placeholder="col.placeholder"
            class="no-border"
            v-if="col.type=='date' || col.type=='datetime'"
            v-model="row[col.name]"
            :type="col.type"
            :disabled="!editable(row, col)"
            value-format="yyyy-MM-dd hh:mm"
            @blur="save(row, col.name)"
          />
          <el-tooltip
            v-if="col.type=='progress'"
            class="item"
            effect="dark"
            :content="`${row[col.name]} of ${row[col.budget]}`"
            placement="top-start"
          >
            <el-progress
              :text-inside="true"
              :stroke-width="24"
              :percentage="progressValue(row[col.name], row[col.budget])"
              :status="progressStatus(row[col.name], row[col.budget])"
            />
          </el-tooltip>

        </template>
      </el-table-column>

      <el-table-column align="right" label="Actions" fixed="right">
        <template slot-scope="{row}">
          <el-button v-if="row.id && detail" class="filter-item pull-right" type="primary" icon="el-icon-edit" @click="detailClicked(row)" />
          <el-button v-if="row.id && allowDelete" class="filter-item pull-right" type="danger" icon="el-icon-remove" @click="remove(row)">
          </el-button>
          <el-button v-if="!row.id" class="filter-item pull-right" type="primary" icon="el-icon-plus" @click="create(row)">
          </el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import list from '@/mixins/list'

export default {
  name: 'GenericList',
  mixins: [list],
  props: ['type', 'detail', 'columns', 'with', 'query', 'order', 'template', 'createBy', 'allowDelete'],
  data() {
    return {
    }
  },  
  methods: {
    progressValue(value, budget) {
      if (!value) return 0
      if (!budget) return 100
      const progress = value / budget
      return Math.round(100.0 * progress)
    },
    progressStatus(value, budget) {
      if (!value) return 'success'
      if (!budget) return 'exception'
      const progress = value / budget
      if (progress <= 0.8) return 'success'
      if (progress <= 1.0) return 'warning'
      return 'exception'
    },
    editable(row, col) {
      if (typeof col.editable == 'function') return col.editable(row)
      else return col.editable
    },
    detailClicked(row) {
      if (typeof this.detail == 'string') {
        this.$router.push(`${this.detail}/${row.id}/detail`)
      } else {
        this.detail(row)
      }
    }
  }
}
</script>

<style scoped type="sass">
.input-disabled {
  padding: 0 15px;
}
</style>
