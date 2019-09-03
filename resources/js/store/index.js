import Vue from 'vue'
import Vuex from 'vuex'

import auth from './auth'
import error from './error'
import debug from './debug'

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        auth,
        error,
        debug,
    }
})

export default store