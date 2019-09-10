const state = {
    mode: 'notice',
    text: null
}
const mutations = {
    setMode (state, mode) {
        state.mode = mode
    },
    setText(state, {text, timeout}){
        state.text = text

        if (typeof timeout === 'undefined'){
            timeout = 3000
        }

        setTimeout(() => (state.text = ''), timeout)
    }
}

export default {
    namespaced: true,
    state,
    mutations
}