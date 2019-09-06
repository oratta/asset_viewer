import Vue from 'vue'
import VueRouter from 'vue-router'

import Login from './pages/Login.vue'
import SystemError from './pages/errors/System.vue'
import NotFound from './pages/errors/NotFound.vue'
import CategoryList from './pages/CategoryList.vue'
import Portfolio from './pages/Portfolio.vue'

import store from './store'

//
Vue.use(VueRouter)

const routes = [
    {
        path: '/login',
        component: Login,
        beforeEnter (to, from, next) {
            if (store.getters['auth/check']) {
                next('/')
            } else {
                next()
            }
        }
    },
    {
        path: '/categories',
        component: CategoryList,
        props: route => {
            const page = route.query.page
            return { page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1 }
        }
    },
    {
        path: '/user_asset',
        component: UserAsset,
    },
    {
        path: '/portfolio',
        component: Portfolio,
    },
    {
        path: '/500',
        component: SystemError
    },

    {
        path: "*",
        component: NotFound
    }

]

const router = new VueRouter({
    mode: 'history',
    routes
})

export default router