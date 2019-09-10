<template>
    <div class="message-bar">
        <div class="message">
            メッセージ→ {{ message }}
        </div>
    </div>
</template>

<script>
    import { mapState } from 'vuex'
    export default {
        computed: {
            ...mapState({
                message: state => state.message.text,
            })
        }

    }
</script>