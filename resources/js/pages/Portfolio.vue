<template>
    <div class="portfolio">
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
                <div class="summary">
                    合計 : ¥{{ sections[sectionTab].current_info.value }}
                </div>
                <div class="pie-chart">

                </div>
                <div class="asset-list">
                    <ul class="asset-list">
                        <li
                                v-for="(asset, index) in sections[sectionTab].children"
                                class="asset-list__item"
                        >
                            {{asset.name}} : ¥{{ asset.current_info.value }} ({{ asset.current_info.rate }}%)
                        </li>
                    </ul>
                </div>
            </div>
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