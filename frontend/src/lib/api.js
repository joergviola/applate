

const base = "http://localhost/gdpr-portal/backend/public"
const oauth= base + "/oauth"
const api= base + "/api"

export default {
  loggedIn: function() {
    return !!this.user
  },
  login: function(email, password) {
    const data = {email, password}
    return fetch(base + '/login',{
      method: "POST", // *GET, POST, PUT, DELETE, etc.
      mode: "cors", // no-cors, cors, *same-origin
      cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
      credentials: "same-origin", // include, *same-origin, omit
      headers: {
        "Content-Type": "application/json",
        // "Content-Type": "application/x-www-form-urlencoded",
      },
      redirect: "follow", // manual, *follow, error
      referrer: "no-referrer", // no-referrer, *client
      body: JSON.stringify(data), // body data type must match "Content-Type" header
    })
      .then(response => {
        if (response.status==200) {
          return response.json()
        }
        throw {
          code : response.status,
          message: response.statusText
        }
      })
  },
  find: function(type, query) {},
  get: function(type, id) {
    return fetch(api + '/' + type + '/' + id)
      .then(response => response.json())
  },
  create: function(type, item) {},
  update: function(type, id, item) {},
  delete: function(type, id) {},
}