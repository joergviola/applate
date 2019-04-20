<template>
    <v-card>
        <v-card-title>
            <h4>{{title}}</h4>
            <v-spacer></v-spacer>
            <v-btn pull-right icon @click="show = !show">
                <v-icon>{{ show ? 'keyboard_arrow_down' : 'keyboard_arrow_up' }}</v-icon>
            </v-btn>
        </v-card-title>

        <v-slide-y-transition>
            <v-card-text v-show="show">
                <slot></slot>
            </v-card-text>
        </v-slide-y-transition>
    </v-card>
</template>

<script>

  export default {
    props: {
        title: String,
        open: {
          type: Boolean,
          default: true
        }
    },
    data: function () {
      return {
        show: this.open,
      }
    },
    watch: {
      open: function(value) {this.show=value}
    }
  }
</script>