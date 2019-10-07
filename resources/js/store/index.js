import Vue from 'vue'
import Vuex from 'vuex'

import auth from './auth'
import error from './error'
import debug from './debug'
import message from './message'

Vue.use(Vuex)

const store = new Vuex.Store({
    modules: {
        auth,
        error,
        debug,
        message,
    }
})

export default store