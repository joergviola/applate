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
          { text: 'Seit', value: 'created_at' },
        ],
      },
    }),
  }
</script>