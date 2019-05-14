<template>
  <div>
    <v-navigation-drawer
            v-model="drawer"
            :clipped="$vuetify.breakpoint.lgAndUp"
            fixed
            app
    >
      <v-list dense>
        <template v-for="item in items">
          <v-layout
                  v-if="item.heading"
                  :key="item.heading"
                  row
                  align-center
          >
            <v-flex xs6>
              <v-subheader v-if="item.heading">
                {{item.heading}}
              </v-subheader>
            </v-flex>
            <v-flex xs6 class="text-xs-center">

            </v-flex>
          </v-layout>
          <v-list-group
                  v-else-if="item.children"
                  :key="item.text"
                  v-model="item.model"
                  :prepend-icon="item.model ? 'keyboard_arrow_up' : 'keyboard_arrow_down'"
                  append-icon=""
          >
            <template v-slot:activator>
              <v-list-tile>
                <v-list-tile-content>
                  <v-list-tile-title>
                    {{item.text}}
                  </v-list-tile-title>
                </v-list-tile-content>
              </v-list-tile>
            </template>
            <v-list-tile
                    v-for="(child, i) in item.children"
                    :key="i"
                    @click=""
            >
              <v-list-tile-action v-if="child.icon">
                <v-icon>{{ child.icon }}</v-icon>
              </v-list-tile-action>
              <v-list-tile-content>
                <v-list-tile-title>
                  <router-link v-if="child.route" :to="child.route">{{child.text}}</router-link>
                  <span v-else>{{ child.text }}</span>
                </v-list-tile-title>
              </v-list-tile-content>
            </v-list-tile>
          </v-list-group>
          <v-list-tile v-else :key="item.text" @click="">
            <v-list-tile-action>
              <v-icon>{{ item.icon }}</v-icon>
            </v-list-tile-action>
            <v-list-tile-content>
              <v-list-tile-title>
                <router-link v-if="item.route" :to="item.route">{{item.text}}</router-link>
                <span v-else>{{ item.text }}</span>
              </v-list-tile-title>
            </v-list-tile-content>
          </v-list-tile>
        </template>
      </v-list>
    </v-navigation-drawer>
    <v-toolbar
            :clipped-left="$vuetify.breakpoint.lgAndUp"
            color="#0099ff"
            dark
            app
            fixed
    >
      <v-toolbar-title style="width: 300px" class="ml-0 pl-3">
        <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
        <span class="hidden-sm-and-down">DSGVO Online</span>
      </v-toolbar-title>
      <v-text-field
              flat
              solo-inverted
              hide-details
              prepend-inner-icon="search"
              label="Search"
              class="hidden-sm-and-down"
      ></v-text-field>
      <v-spacer></v-spacer>
      <v-btn icon>
        <v-icon>apps</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>notifications</v-icon>
      </v-btn>
      <v-btn icon large>
        <v-avatar size="32px" tile>
          {{user.name}}
          <v-menu bottom right>
            <template v-slot:activator="{ on }">
              <v-btn
                      dark
                      icon
                      v-on="on"
              >
                ({{notifications.length}})
              </v-btn>
            </template>

            <v-list>
              <v-list-tile
                      v-for="item in notifications"
                      :key="item.id"
                      @click=""
              >
                <v-list-tile-title>
                  <router-link :to="{name:item.type+'-edit', params: {id: item.item_id}}">
                  {{ item.operation }}
                  {{ item.type }}:
                  {{ item.item_id }} by
                  {{ item.user.name }}
                  </router-link>
                </v-list-tile-title>
              </v-list-tile>
            </v-list>
          </v-menu>
        </v-avatar>
      </v-btn>
    </v-toolbar>
    <v-content>
      <v-container fluid fill-height>
        <v-layout justify-center align-center>
          <router-view></router-view>
        </v-layout>
      </v-container>
    </v-content>
  </div>
</template>

<script>

  import api from "@/lib/api.js"

  export default {
    data: () => ({
      dialog: false,
      drawer: null,
      user: api.user(),
      notifications: [],
      items: [
        { icon: 'contacts', text: 'Verfahren', route: '/verfahren' },
        { text: 'Benutzer', children: [
            { icon: 'person', text: 'Benutzer', route: '/user' },
            { icon: 'settings', text: 'Rollen & Rechte', route: '/roles' },
        ] },
        { icon: 'content_copy', text: 'TOM', route: '/about' },
        { icon: 'settings', text: 'Settings' },
        { icon: 'chat_bubble', text: 'Send feedback' },
        { icon: 'help', text: 'Help' },
        { icon: 'phonelink', text: 'App downloads' },
        { icon: 'keyboard', text: 'Go to the old version' }
      ]
    }),
    props: {
      source: String
    },
    created() {
      api.getNotifications().then(items => this.notifications = items)
    }
  }
</script>