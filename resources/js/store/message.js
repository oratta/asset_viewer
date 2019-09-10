const state = {
    code: null,
    text: "test"
}
const mutations = {
    setCode (state, code) {
        state.code = code
    },
    setText(state, text){
        state.text = text
    }
}

export default {
    namespaced: true,
    state,
    mutations
}