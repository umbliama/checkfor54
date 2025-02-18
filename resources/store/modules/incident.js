export default {
    namespaced: true,
    state: () => ({
        activeMenuItem: 'tasks',
        selectedEquipment: null
    }),
    mutations: {
        setSelectedEquipment:(state, equip) => {
            state.selectedEquipment = equip
        },
        setActiveMenuItem:(state,activeMenuItem) => {
            state.activeMenuItem = activeMenuItem
        }
    },
    actions: {
        updateSelectedEquipment({commit}, equip) {
            commit('setSelectedEquipment', equip)
        },
        updateActiveMenuItem({commit}, activeMenuItem){
            commit('setActiveMenuItem', activeMenuItem)
        }
    },
    getters: {
        getActiveMenuItem:(state) => state.activeMenuItem,
        getSelectedEquipment:(state) => state.selectedEquipment
    },
};
