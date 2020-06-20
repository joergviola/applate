import Vue from 'vue'
import moment from 'moment'

Vue.filter('dateHuman', function (value) {
  if (!value) return ''

  if (!(value instanceof Date) )
    value = new Date(value);
  return moment(value).fromNow();
})

Vue.filter('datetime', function (value) {
  if (!value) return ''
  if (!(value instanceof Date) )
    value = new Date(value);

 // return value.toLocaleString()
  return formatDateTime(value);
})

Vue.filter('currency', (value, currency = 'EUR', currencyDisplay = 'code') => {
  let v = 0
  try {
    if (typeof value === 'string') {
      v = parseInt(value)
    }
    v = value
  } catch (error) {
    v = 0
  }

  return new Intl.NumberFormat('de-DE', {
    style: 'currency',
    currencyDisplay,
    currency,
  }).format(v)
})

Vue.filter('number', (value) => {
  let v = 0
  if (value === null) {
    return ''
  }

  try {
    if (typeof value === 'string') {
      v = parseInt(value)
    }

    v = value
  } catch (error) {
    v = 0
  }

  return v.toLocaleString('de-DE', { style: 'decimal' })
})

Vue.filter('fileSize', size => {
    if (size < 1024) {
      return size + ' B'
    }
    const i = Math.floor(Math.log(size) / Math.log(1024))
    let num = (size / Math.pow(1024, i))
    const round = Math.round(num)
    num = round < 10 ? num.toFixed(2) : round < 100 ? num.toFixed(1) : round
    return `${num} ${'KMGTPEZY'[i-1]}B`
})

Vue.filter('material', mat => {
  if (mat.variant==null) return mat.mat_no;
  if (mat.variant.InternalDescription==null) return mat.VariantCode
  return mat.variant.InternalDescription
  // const fields = mat.variant.InternalDescription.split(',')
  // if (fields.length>=2) {
  //   return fields[1]
  // } else {
  //   return fields[0]
  // }
})

Vue.filter('firstmaterial', (pos, type) => {
  if (!pos.materials) return null
  const mats = pos.materials.filter(m=>m.type==type)
  if (mats.length==0) return null
  const mat = mats[0]
  if (mat.variant==null) return mat.mat_no
  if (mat.variant.InternalDescription==null) return mat.VariantCode
  return mat.variant.InternalDescription
  // const fields = mat.variant.InternalDescription.split(',')
  // if (fields.length>=2) {
  //   return fields[1]
  // } else {
  //   return fields[0]
  // }
})

