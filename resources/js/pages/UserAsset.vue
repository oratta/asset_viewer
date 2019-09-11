<template>
    <div class="user-asset">
        <div class="container">
            <h2>Your Asset List</h2>
            <table>
                <thead>
                    <th>account name</th>
                    <th>asset name</th>
                    <th>value</th>
                    <th v-for="sectionInfo in this.sectionInfos">
                        {{ sectionInfo[0] }}
                    </th>
                </thead>
                <tbody>
                    <tr v-for="userAsset in this.userAssets">
                        <td>
                            {{ userAsset.account }}
                        </td>
                        <td>
                            {{ userAsset.name }}
                        </td>
                        <td>
                            {{ userAsset.value }}
                        </td>
                        <td>
                            <select></select>
                        </td>
                        <td>
                            <select></select>
                        </td>
                        <td>
                            <select></select>
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
                this.sectionInfos = response.data.sectionInfos;
                this.userAssets = response.data.userAssets;
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