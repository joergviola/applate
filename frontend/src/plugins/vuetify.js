import 'material-design-icons-iconfont/dist/material-design-icons.css' // Ensure you are using css-loader

import Vue from 'vue'
import Vuetify from 'vuetify/lib'
import 'vuetify/src/stylus/app.styl'
import de from 'vuetify/es5/locale/de'

import 'typeface-roboto/index.css';

Vue.use(Vuetify, {
  theme: {
    primary: '#0099ff',
    secondary: '#0099ff',
    accent: '#0099ff',
    error: '#FF5252',
    info: '#2196F3',
    success: '#4CAF50',
    warning: '#FFC107'
  },
  iconfont: 'md',
  lang: {
    locales: { de },
    current: 'de'
  },
})
