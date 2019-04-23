<template>
  <data-table title="Rollen" type="role" :cfg="cfg"></data-table>
</template>

<script>

  import DataTable from "../../components/DataTable";

  export default {
    components: {DataTable},
    data: () => ({
      cfg: {
        columns: [
          { text: 'Name', value: 'name' },
        ],
      },
    }),
  }
</script>