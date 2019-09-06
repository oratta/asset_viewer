<template>
    <footer class="footer">
        <div class="footer">
            <button v-if="isLogin" class="button button--link" @click="logout">
                Logout
            </button>
            <RouterLink v-else class="button button--link" to="/login">
                Login / Register
            </RouterLink>
        </div>
        <Debug/>
    </footer>
</template>

<script>
    import { mapState, mapGetters } from 'vuex'
    import Debug from '../components/Debug.vue'

    export default {
        components: {
            Debug
        },
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus
            }),
            ...mapGetters({
                isLogin: 'auth/check'
            })
        },
        methods: {
            async logout () {
                await this.$store.dispatch('auth/logout')

                if (this.apiStatus) {
                    this.$router.push('/login')
                }
            }
        }
    }
</script>