<template>
    <div class="user-asset">
        <div class="container">
        </div>
    </div>
</template>

<script>
    import { OK } from '../util'
    export default {
        data () {
            return {
                uAssets:[],
            }
        },
        methods: {
            async fetchUserAsset () {
                const response = await axios.get(`/api/user_asset/`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.$store.commit('debug/setObj', response)
                this.sections = response.data
            },
        },
        watch: {
            $route: {
                async handler () {
                    await this.fetchUserAsset()
                },
                immediate: true
            }
        }

    }
</script>