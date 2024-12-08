export default {
    namespaced: true,
    state: () => ({
        activeMenuItem: 'tasks'
    }),
    mutations: {
        setActiveMenuItem:(state,activeMenuItem) => {
            state.activeMenuItem = activeMenuItem
        }
    },
    actions: {
        updateActiveMenuItem({commit}, activeMenuItem){
            commit('setActiveMenuItem', activeMenuItem)
        }
    },
    getters: {
        getActiveMenuItem:(state) => state.activeMenuItem
    },
};
