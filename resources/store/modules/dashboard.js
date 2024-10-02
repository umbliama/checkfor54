import { EquipMenuItems } from "../../constants";

export default {
    namespaced: true,
    state: () => ({
        activeTab:"analysis"
    }),
    mutations: {
        setActiveTab:(state,tab) => {
            state.activeTab = tab
        }
    },
    actions: {
        updateActiveTab({commit}, tab) {
            commit('setActiveTab', tab)
        }
    },
    getters: {
        getUpdateActiveTab: (state) => state.activeTab
    },
};
