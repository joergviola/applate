<template>
  <el-tooltip
    class="item"
    effect="dark"
    :content="`${used} of ${planned}`"
    placement="top-start"
  >
    <el-progress
      :text-inside="true"
      :stroke-width="width || 24"
      :percentage="percentage"
      :status="status"
    />
</el-tooltip>

</template>

<script>
export default {
  name: 'Progress',
  props: ['used', 'planned', 'width'],
  computed: {
    percentage() {
      if (!this.used) return 0
      if (!this.planned) return 100
      const progress = this.used<this.planned 
        ? this.used / this.planned
        : this.planned / this.used
      return Math.round(100.0 * progress)
    },
    status() {
      if (!this.used) return 'success'
      if (!this.planned) return 'exception'
      const progress = this.used / this.planned
      if (progress <= 0.8) return 'success'
      if (progress <= 1.0) return 'warning'
      return 'exception'
    }
  },
}
</script>

