export default {
    namespaced: true,
    state: () => ({
        activeTab: "sale",
        extraServices: []
    }),
    mutations: {
        setActiveTab(state, activeTab) {
            state.activeTab = activeTab;
        },
        updateExtraService(state, service) {
            const index = state.extraServices.findIndex(s => s.id === service.id);
            if (index !== -1) {
                state.extraServices.splice(index, 1, service);
            }
        },
        addExtraService(state, service) {
            state.extraServices.push(service);
        }
    },
    actions: {
        updateExtraServices({ state, commit }, service) {
            console.log(service)
            commit('addExtraService', service);
        },
        updateActiveTab({ commit }, payload) {
            commit('setActiveTab', payload)
        },
    },
    getters: {
        getActiveTab: (state) => state.activeTab
    },
};
