<template>
  <div>
    <el-form-item v-for="(field,i) in fields" :key="i" :label="field.label">
      <el-input v-if="field.type=='textarea'" v-model="item[field.name]" :rows="1" type="textarea" autosize :placeholder="field.placeholder" />
      <el-select v-else-if="field.type=='select'" v-model="item[field.name]">
        <el-option v-for="(o, i) in field.options" :key="i" :label="o" :value="o" />
      </el-select>
      <el-date-picker
        v-else-if="field.type=='date'"
        v-model="item[field.name]"
        :type="field.type"
        value-format="yyyy-MM-dd"
      />
      <to-one
        v-else-if="field.type=='to-one'"
        v-model="item[field.name]"
        :type="field.ref"
        :display="field.display"
        :link="field.link"
        :query="field.query"
        @input="id => field.input ? field.input(id) : null"
      />
      <el-input v-else-if="field.type=='password'" show-password v-model="item[field.name]" />
      <el-input v-else type="text" :disabled="field.disabled" v-model="item[field.name]"  >
        <template v-if="field.postfix" slot="append">{{field.postfix}}</template>
      </el-input>
    </el-form-item>
  </div>
</template>

<script>

import ToOne from './ToOne'

export default {
  name: 'GenericDetails',
  components: { ToOne },
  props: ['item', 'fields'],
}
</script>

<style lang="scss" scoped>
</style>
