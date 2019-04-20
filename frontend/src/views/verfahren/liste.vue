<template>
  <div class="elevation-10">
    <v-toolbar flat color="white">
      <v-toolbar-title>Verfahren</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-text-field
              v-model="search"
              append-icon="search"
              label="Search"
              single-line
              hide-details
      ></v-text-field>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog">
        <template v-slot:activator="{ on }">
          <v-fab-transition>
          <v-btn fab color="primary" dark class="mb-1" v-on="on"  small
                 absolute
                 top
                 right>
            <v-icon dark>add</v-icon>
          </v-btn>
          </v-fab-transition>
        </template>
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

          <v-card-text>
            <v-container grid-list-md>
              <v-layout wrap>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.name" label="Name"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.target" label="Betroffen"></v-text-field>
                </v-flex>
                <v-flex xs12 sm6 md4>
                  <v-text-field v-model="editedItem.data" label="Datentöpfe"></v-text-field>
                </v-flex>
              </v-layout>
            </v-container>
          </v-card-text>

          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" flat @click="close">Cancel</v-btn>
            <v-btn color="blue darken-1" flat @click="save">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-toolbar>
    <v-data-table
            :headers="headers"
            :items="items"
            :search="search"
    >
      <template v-slot:items="props">
        <td>{{ props.item.name }}</td>
        <td>{{ props.item.target }}</td>
        <td>{{ props.item.data }}</td>
        <td >
          <router-link :to="{ name: 'verfahren-edit', params: { id: 123 }}"><v-icon
                  small
                  class="mr-2"
          >
            keyboard_arrow_right
          </v-icon></router-link>
          <!--<v-icon-->
                  <!--small-->
                  <!--class="mr-2"-->
                  <!--@click="editItem(props.item)"-->
          <!--&gt;-->
            <!--edit-->
          <!--</v-icon>-->
          <!--<v-icon-->
                  <!--small-->
                  <!--@click="deleteItem(props.item)"-->
          <!--&gt;-->
            <!--delete-->
          <!--</v-icon>-->
        </td>
      </template>
      <template v-slot:no-data>
        <v-btn color="primary" @click="initialize">Reset</v-btn>
      </template>
    </v-data-table>
  </div>
</template>

<script>
  export default {
    data: () => ({
      search: '',
      dialog: false,
      headers: [
        { text: 'Name', value: 'name' },
        { text: 'Betroffen', value: 'targets' },
        { text: 'Datenhaltung', value: 'data' },
        { text: 'Aktionen' },
      ],
      items: [],
      editedIndex: -1,
      editedItem: {
        name: '',
        betroffen: '',
        data: '',
      },
      defaultItem: {
        name: '',
        betroffen: '',
        data: '',
      }
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
      }
    },

    created () {
      this.initialize()
    },

    methods: {
      initialize () {
        this.items = [
          {
            name: 'Office 365 Mail',
            target: 'Besucher Webseite, Mitarbeiter, Kunden',
            data: 'Office 365 Mail',
          },
          {
            name: 'Google Analytics',
            target: 'Besucher Webseite',
            data: 'Google Analytics',
          },
          {
            name: 'Newsletter',
            target: 'Interessenten, Kunden',
            data: 'MailChimp',
          },
        ]
      },

      editItem (item) {
        this.editedIndex = this.items.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        const index = this.items.indexOf(item)
        confirm('Are you sure you want to delete this item?') && this.items.splice(index, 1)
      },

      close () {
        this.dialog = false
        setTimeout(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        }, 300)
      },

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.items[this.editedIndex], this.editedItem)
        } else {
          this.items.push(this.editedItem)
        }
        this.close()
      }
    }
  }
</script>