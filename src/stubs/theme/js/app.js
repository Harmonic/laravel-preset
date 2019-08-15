require('./bootstrap')

Vue.mixin({ methods: { route: (...args) => window.route(...args).url() } })

import { InertiaApp } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue'
import Vue from 'vue'
Vue.use(InertiaApp)
Vue.use(PortalVue)

let app = document.getElementById('app')

new Vue({
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: (name) => {
        return import(`@/Pages/${name}`).then(module => module.default)
      },
    },
  }),
}).$mount(app)
