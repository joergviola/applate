

const base = window.location.origin + window.location.pathname + "/api/"

export default {
  loggedIn: function() {
    return !!this.user
  },
  login: function(username, password) {},
  find: function(type, query) {},
  get: function(type, id) {
    return fetch(base + type + '/' + id)
      .then(response => response.json())
  },
  create: function(type, item) {},
  update: function(type, id, item) {},
  delete: function(type, id) {},
}