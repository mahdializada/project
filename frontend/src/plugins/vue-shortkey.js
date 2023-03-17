import Vue from 'vue'

const ShortKey = require('vue-shortkey')
Vue.use(ShortKey, { prevent: ['.vue-shortkey-avoid', 'textarea', 'input'] })

export default ShortKey