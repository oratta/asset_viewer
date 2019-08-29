<template>
    <div class="category-list">
        <ul>
            <li v-for="category in categories">
                {{ category.name }}
            </li>
        </ul>
        <Pagination :current-page="currentPage" :last-page="lastPage" />
    </div>
</template>
<script>
    import { OK } from '../util'
    import Pagination from '../components/Pagination.vue'

    export default {
        components: {
            Pagination
        },
        props: {
            page: {
                type: Number,
                required: false,
                default: 1
            }
        },
        data () {
            return {
                categories: [],
                currentPage: 0,
                lastPage: 0
            }
        },
        methods: {
            async fetchPhotos () {
                const response = await axios.get(`/api/categories/?page=${this.page}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.categories = response.data.data
                this.currentPage = response.data.current_page
                this.lastPage = response.data.last_page
            },
        },
        watch: {
            $route: {
                async handler () {
                    await this.fetchPhotos()
                },
                immediate: true
            }
        }
    }
</script>