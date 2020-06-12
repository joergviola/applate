import router from '@/router'

const host = window.location.hostname === 'localhost'
  ? 'http://localhost/promise/backend/public'
  : window.location.origin + window.location.pathname + '/../..'

const base = host + '/api/v1.0'

function callDirect(url, options) {
  const user = theAPI.user()
  if (user) {
    options.headers['Authorization'] = 'Bearer ' + user.token
  }

  return fetch(base + url, options)
    .then(response => {
      if (response.status == 200) {
        return response.json()
      } else if (response.status == 401) {
        storage.remove('user')
        router.go('/')
      } else {
        return response.json().then(r => {
          throw {
            status: {
              code: response.status,
              text: response.statusText
            },
            message: r.message
          }
        })
      }
    })
}

function call(method, url, data) {
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
  return callDirect(url, {
    method: method, // *GET, POST, PUT, DELETE, etc.
    mode: 'cors', // no-cors, cors, *same-origin
    cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
    credentials: 'same-origin', // include, *same-origin, omit
    headers: headers,
    redirect: 'follow', // manual, *follow, error
    referrer: 'no-referrer', // no-referrer, *client
    body: JSON.stringify(data) // body data type must match "Content-Type" header
  })
}

const storage = {
  set(key, value) {
    if (!value) {
      window.localStorage.removeItem(key)
    } else {
      window.localStorage.setItem(key, JSON.stringify(value))
    }
  },
  get(key) {
    const value = window.localStorage.getItem(key)
    if (!value) return value
    return JSON.parse(value)
  },
  remove(key) {
    window.localStorage.removeItem(key)
  }
}

const theAPI = {
  user: function() {
    return storage.get('user')
  },
  userCan: function(type, actions) {
    const rights = this.user().role.rights
      .filter(right => right.tables=='*' || right.tables.search(type)!=-1)
      .filter(right => actions.indexOf(right.actions)!=-1)
    return rights.length>0
  },
  login: function(email, password) {
    return call('POST', '/../../login', { email, password })
      .then(user => {
        storage.set('user', user)
        return user
      })
  },
  logout: function() {
    storage.remove('user')
  },
  register: function(email, password, name) {
    return call('POST', '/user', { email, password, name })
  },
  find: function(type, query) {
    return call('POST', '/' + type + '/query', query)
  },
  findFirst: async function(type, query) {
    const result = await this.find(type, query)
    if (result.length === 0) return null
    return result[0]
  },
  get: function(type, id) {
    return call('GET', '/' + type + '/' + id)
  },
  create: function(type, item) {
    return call('POST', '/' + type, item)
  },
  update: function(type, id, item) {
    return call('PUT', '/' + type + '/' + id, item)
  },
  updateBulk: function(type, items) {
    return call('PUT', '/' + type, items)
  },
  createOrUpdate: async function(type, item) {
    if (item.id) {
      await this.update(type, item.id, item)
    } else {
      const result = await this.create(type, item)
      item.id = result.id
    }
  },
  delete: function(type, id) {
    return call('DELETE', '/' + type + '/' + id)
  },
  log: function(type, id) {
    return call('GET', '/' + type + '/' + id + '/log')
  },
  restore: function(type, log) {
    return call('PUT', '/' + type + '/restore/' + log)
  },
  getNotifications: function() {
    return call('GET', '/notifications')
  },
  createNotifications: function() {
    return call('DELETE', '/notifications')
  },
  createDocs: function(type, id, files) {
    var data = new FormData()
    for (const key in files) {
      for (const file of files[key]) {
        data.append(key, file, file.name)
      }
    }
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    }

    return callDirect('/' + type + '/' + id + '/documents', {
      method: 'POST', // *GET, POST, PUT, DELETE, etc.
      mode: 'cors', // no-cors, cors, *same-origin
      cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
      credentials: 'same-origin', // include, *same-origin, omit
      headers: {},
      redirect: 'follow', // manual, *follow, error
      referrer: 'no-referrer', // no-referrer, *client
      body: data // body data type must match "Content-Type" header
    })
  },
  getDocs: function(type, id) {
    return call('GET', '/' + type + '/' + id + '/documents')
  },
  removeDocs: function(type, id, docIds) {
    return call('DELETE', '/' + type + '/' + id + '/documents/' + docIds)
  },
  datetime: function(value = null) {
    if (!value) return null
    return value.toISOString().slice(0, 19).replace('T', ' ')
  },
  clone: function(orig, options) {
    const copy = Object.assign({}, orig, options.mask || {})
    delete copy.id
    if (orig._meta) {
      copy._meta = Object.assign({}, orig._meta)
    }
    if (options.refs) {
      for (const ref in options.refs) {
        const meta = copy._meta[ref]
        meta.ignore = false
        if (meta.many) {
          copy[ref] = orig[ref].map(o => this.clone(o, options.refs[ref]))
        }
        if (meta.one) {
          copy[ref] = this.clone(orig[ref], options.refs[ref])
        }
      }
    }
    return copy
  }
}

export default theAPI
