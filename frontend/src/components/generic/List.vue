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

    <el-table ref="theTable" v-loading="loading" :data="list" row-key="id">
      <el-table-column v-if="sort" label="" width="25">
        <template slot-scope="{row, $index}">
          <i class="el-icon-menu"></i>
        </template>
      </el-table-column>
      <el-table-column v-for="(col,i) in columns" :key="i" :label="col.label" :prop="col.name" :minWidth="col.width" sortable>
        <template slot-scope="{row, $index}">
          <el-input
            v-if="editable(row, col) && !col.type"
            class="no-border"
            v-model="row[col.name]"
            :disabled="!editable(row, col) || readonly"
            @change="save(row, col.name)"
            :placeholder="col.placeholder"
            :ref="`field-${$index}-${i}`"
            @keydown.enter.native="onEnter(row, i, $index)"
            @keydown.up.native="onArrow(i, $index, -1)"
            @keydown.down.native="onArrow(i, $index, +1)"
          />
          <span v-if="!editable(row, col) && !col.type" class="input-disabled">{{typeof col.name === 'string' ? _.get(row, col.name) : col.name(row) }}</span>
          <el-select v-if="col.type=='select'" class="no-border" v-model="row[col.name]" @change="save(row, col.name)"  :placeholder="col.placeholder">
            <el-option v-for="(o, i) in col.options" :key="i" :label="col.display ? _.get(o, col.display) : o" :value="col.id ? _.get(o, col.id) : o" />
          </el-select>
          <el-date-picker
            :placeholder="col.placeholder"
            class="no-border"
            v-if="col.type=='date' || col.type=='datetime'"
            v-model="row[col.name]"
            :type="col.type"
            :disabled="!editable(row, col) || readonly"
            value-format="yyyy-MM-dd hh:mm"
            @change="save(row, col.name)"
          />
          <progress-bar
            v-if="col.type=='progress'"
            :used="row[col.name]"
            :planned="row[col.budget]"
          />
        </template>
      </el-table-column>

      <el-table-column align="right" label="Actions" fixed="right">
        <template slot-scope="{row}">
          <i v-if="row.id && allowDelete" class="action el-icon-remove-outline" @click="remove(row)" title="Delete this line"/> 
          <i v-if="row.id && detail" class="action el-icon-arrow-right" @click="detailClicked(row)"  title="Edit details"/> 
          <i v-if="!row.id" class="action el-icon-circle-plus-outline" @click="create(row)"  title="Create a new line"/> 
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import list from '@/mixins/list'
import ProgressBar from './Progress'

export default {
  name: 'GenericList',
  mixins: [list],
  components: {ProgressBar},
  props: ['type', 'detail', 'columns', 'with', 'query', 'order', 'template', 'createBy', 'allowDelete', 'sort'],
  methods: {
    editable(row, col) {
      if (this.readonly) return false
      if (typeof col.editable == 'function') return col.editable(row)
      else return col.editable
    },
    detailClicked(row) {
      if (typeof this.detail == 'string') {
        this.$router.push(`${this.detail}/${row.id}/detail`)
      } else {
        this.detail(row)
      }
    },
  }
}
</script>

<style scoped type="sass">
.input-disabled {
  padding: 0 15px;
}
i.action {
  color: #AAAAAA;
  cursor: pointer;
  font-size: 150%;
  margin-top: 10px;
  margin-left: 10px;
}
i {
  color: #EEEEEE;
  cursor: grab;
}
</style>
