<template>
    <div class="user-asset">
        <div class="container">
            <h2>Your Asset List</h2>
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
                                <select :name="userAsset.id + '_' + categories[0].section_id">
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
        </div>
    </div>
</template>

<script>
    import { OK } from '../util'
    export default {
        data () {
            return {
                uAssets: [],
                sections: [],
                sectionInfos: [],
                userAssets: [],
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
                this.sections = response.data.sections
                this.sectionInfos = response.data.sectionInfos
                this.userAssets = response.data.userAssets
                for (var i = 0, len = this.userAssets.length; i < len; ++i) {
                    this.userAssets[i].sectionInfos = this.sectionInfos
                }
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