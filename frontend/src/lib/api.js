const base = "http://localhost/gdpr-portal/backend/public/api/v1.0"

function call(method, url, data) {
  const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
  }
  const user = theAPI.user()
  if (user) {
    headers["Authorization"] = "Bearer " + user.token
  }
  return fetch(base + url, {
    method: method, // *GET, POST, PUT, DELETE, etc.
    mode: "cors", // no-cors, cors, *same-origin
    cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
    credentials: "same-origin", // include, *same-origin, omit
    headers: headers,
    redirect: "follow", // manual, *follow, error
    referrer: "no-referrer", // no-referrer, *client
    body: JSON.stringify(data), // body data type must match "Content-Type" header
  })
    .then(response => {
      if (response.status == 200) {
        return response.json()
      }
      throw {
        code: response.status,
        message: response.statusText
      }
    })
}

const storage = {
  set(key, value) {
    window.localStorage.setItem(key, JSON.stringify(value));
  },
  get(key) {
    const value = window.localStorage.getItem(key);
    if (!value) return value;
    return JSON.parse(value);
  },
  remove(key) {
    window.localStorage.removeItem(key);
  },
}

const theAPI = {
  user: function () {
    return storage.get('user')
  },
  login: function (email, password) {
    return call('POST', '/../login', {email, password})
      .then(user => storage.set('user', user))
  },
  register: function (email, password, name) {
    return call('POST', '/user', {email, password, name})
  },
  find: function (type, query) {
    return call('POST',  '/' + type + '/query', query)
  },
  get: function (type, id) {
    return call('GET',  '/' + type + '/' + id)
  },
  create: function (type, item) {
  },
  update: function (type, id, item) {
  },
  delete: function (type, id) {
  },
}

export default theAPI;