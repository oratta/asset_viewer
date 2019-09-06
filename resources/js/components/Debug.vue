<template>
    <div class="debug">
        <h2>Debug Menue</h2>
        <div class="debug__item">
            <h3>Data set</h3>
            <form class="form" @submit.prevent="addAsset">
                <label for="count">asset count</label>
                <input type="text" class="form__item" id="count" v-model="addAssetForm.assetCount">
                <div class="form__button">
                    <button type="submit" class="button button--inverse">submit</button>
                </div>
            </form>
            <p>ログインユーザのUserAssetをランダムで設定します</p>
        </div>
    </div>
</template>

<script>
    import { CREATED } from '../util'
    export default{
        data(){
            return {
                addAssetForm: {
                    count: 0,
                }
            }
        },
        methods: {
            async addAsset () {
                const response = await axios.post('/api/debug/addCount', this.addAssetForm)

                if (response.status !== CREATED){
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.$store.commit('debug/setObj', response)
            },
        },
    }
</script>