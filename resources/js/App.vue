<template>
    <div>
        <header>
            <Navbar />
        </header>
        <main>
            <Message />
            <div class="container">
                <RouterView />
            </div>
        </main>
        <Footer />
    </div>
</template>

<script>
    import Navbar from './components/Navbar.vue'
    import Footer from './components/Footer.vue'
    import Message from './components/Message.vue'

    import { INTERNAL_SERVER_ERROR, UNAUTHORIZED, NOT_FOUND } from "./util";

    export default {
        components: {
            Navbar,
            Footer,
            Message,
        },
        computed: {
            errorCode () {
                return this.$store.state.error.code
            }
        },
        watch: {
            errorCode: {
                async handler (val) {
                    if (val === INTERNAL_SERVER_ERROR) {
                        this.$router.push('/500')
                    } else if (val === UNAUTHORIZED) {
                        // トークンをリフレッシュ
                        await axios.get('/api/refresh-token')
                        // ストアのuserをクリア
                        this.$store.commit('auth/setUser', null)

                        //メッセージ表示
                        this.$store.commit('message/setText', {
                            text: 'Please login the system.',
                            timeout: 6000
                        })

                        // ログイン画面へ
                        this.$router.push('/login')
                    } else if (val === NOT_FOUND){
                        this.$router.push('/not-found')
                    }
                },
                immediate: true
            },
            $route () {
                this.$store.commit('error/setCode', null)
                this.$store.commit('error/setMessage', null)
            }
        },
    }
</script>