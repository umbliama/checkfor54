
export default {
    namespaced: true,
    state: () => ({
        showForm: false
    }),
    mutations: {
        setShowForm(state, value) {
            state.showForm = value
        }
     },
    actions: {
        updateShowForm({commit},value) {
            commit('setShowForm',value)
        }
    },
    getters: {
        getShowForm: (state) => state.showForm
    },
};
