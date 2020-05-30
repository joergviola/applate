<template>
    
  <div class="components-container">
    <el-tabs v-model="activeTab">
      <el-tab-pane key="1" label="Roles" name="1">
        <generic-list
            type="role"
            :detail="showRole"
            :columns="roles.columns"
            :with="roles.w"
            :template="roles.template"
            :allowDelete="true"
        />
      </el-tab-pane>
      <el-tab-pane v-if="role" key="2" label="Rights" name="2">
        <generic-list
            type="right"
            :columns="rights.columns"
            :with="rights.w"
            :template="rightTmpl"
            :allowDelete="true"
        />
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import GenericList from '@/components/generic/List'

export default {
  name: 'Roles',
  components: { GenericList },
  data() {
    return {
      activeTab: "1",
      role: null,
      roles: {
        template: {  },
        w: { },
        columns: [
            { name: 'name', label: 'Name', editable: true },
        ]
      },
      rights: {
        w: { },
        columns: [
            { name: 'tables', label: 'Tables', editable: true },
            { name: 'columns', label: 'Columns', editable: true },
            { name: 'where', label: 'Where', editable: true },
            { name: 'actions', label: 'Actions', editable: true },
        ]
      },
    }
  },
  computed: {
      rightTmpl() {
          return {role_id: this.role.id}
      }
  },
  methods: {
      showRole(role) {
          this.role = role
          this.activeTab = "2"
      }
  }
}
</script>

<style scoped type="sass">
</style>
