<template>
    <div class="user-asset">
        <div class="container">
            <h2>Your Asset List</h2>
            <Loader v-show="loading">loading your data</Loader>
            <Loader v-show="categorizing">processing to categorize your data</Loader>
            <div v-show="! loading">
                <form class="form" @submit.prevent="categorize">
                    <table>
                        <thead>
                        <th>account name</th>
                        <th>asset name</th>
                        <th>value</th>
                        <th v-for="(section, sectionId) in this.sections">
                            {{ section.name }}
                        </th>
                        </thead>
                        <tbody>
                        <tr v-for="userAsset in this.userAssets">
                            <td class="user-asset__data">
                                {{ userAsset.account }}
                            </td>
                            <td class="user-asset__data">
                                {{ userAsset.name }}
                            </td>
                            <td class="user-asset__data">
                                {{ userAsset.value }}
                            </td>
                            <td v-for="categories in userAsset.sectionInfos">
                                <div class="user-asset__select sl">
                                    <select v-model="userAsset['categoryIds'][categories[0].section_id]">
                                        <option selected="selected">select...</option>
                                        <option v-for="category in categories" :value="category.id">
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="form__button">
                        <Loader v-show="categorizing">processing to categorize your data</Loader>
                        <button v-bind:disabled="categorizing" type="submit" class="button button--inverse">categorize</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</template>

<script>
    import { OK, CREATED } from '../util'
    import Loader from '../components/Loader.vue'

    export default {
        components: {
            Loader,
        },
        data () {
            return {
                sections: [],
                sectionInfos: [],
                userAssets: [],
                loading: false,
                categorizing: false,
            }
        },
        computed: {
            categorizeForm(){
                var returnArr = {}
                this.userAssets.forEach(function(uAsset){
                    for (var sectionId=1;sectionId<=3;sectionId++){
                        var key = uAsset.id + "-" + sectionId
                        returnArr[key] = uAsset['categoryIds'][sectionId]
                    }
                })
                return returnArr
            },
        },
        methods: {
            async fetchUserAsset () {
                this.loading = true
                const response = await axios.get(`/api/user_asset/`)
                this.loading = false

                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.$store.commit('debug/setObj', response)
                this.sections = response.data.sections
                this.sectionInfos = response.data.sectionInfos
                this.userAssets = response.data.userAssets
                for (var i = 0, len = this.userAssets.length; i < len; ++i) {
                    this.userAssets[i].sectionInfos = this.sectionInfos
                }
            },
            async categorize () {
                this.categorizing = true
                const response = await axios.post('/api/categorize', this.categorizeForm)
                this.categorizing = false

                if (response.status !== CREATED) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.$store.commit('message/setText', {
                    text: 'Update your assets categorize.',
                    timeout: 6000
                })
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