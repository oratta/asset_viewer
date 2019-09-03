const state = {
    obj: null,
}
const mutations = {
    setObj(state, obj){
        state.obj = obj
    }
}

export default {
    namespaced: true,
    state,
    mutations
}