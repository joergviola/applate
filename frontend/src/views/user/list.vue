<template>
  <data-table title="Benutzer" type="users" :cfg="cfg"></data-table>
</template>

<script>

  import DataTable from "../../components/DataTable";

  export default {
    components: {DataTable},
    data: () => ({
      cfg: {
        columns: [
          { text: 'Name', value: 'name' },
          { text: 'Email', value: 'email' },
          { text: 'Rolle', value: 'role.name' },
        ],
        query : {
          with: {
            role: {type:'role', from: 'role_id'}
          }
        }
      },
    }),
  }
</script>