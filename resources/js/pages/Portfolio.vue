<template>
    <div class="container--small">
        <ul class="tab">
            <li
                    v-for="(section, id) in sections"
                    class="tab__item"
                    :class="{'tab__item--active': sectionTab === id }"
                    @click="sectionTab = id"
            >{{ section.name }}</li>
        </ul>
        <div class="panel">
            {{ sections[sectionTab].name }}
        </div>
    </div>
</template>

<script>
    import { OK } from '../util'
    export default {
        data () {
            return {
                sections:[],
                sectionTab: 1,
            }
        },
        methods: {
            async fetchPortfolio () {
                const response = await axios.get(`/api/portfolio/`)
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
                    await this.fetchPortfolio()
                },
                immediate: true
            }
        }

    }
</script>