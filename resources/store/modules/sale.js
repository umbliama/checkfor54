export default {
    namespaced: true,
    state: () => ({
        activeTab: "sale",
        currentIndex: 0,
        rowsCount: 0,
        extraServicesModal: false,
        selectedServices: [], 
        selectedEquipment: [], 
        activeMainEquipmentId: null,
        activeSubEquipmentId: null,
    }),
    mutations: {
        incrementRowsCount(state) {
            state.rowsCount += 1;
        },
        decrementRowsCount(state) {
            state.rowsCount -= 1;
        },
        setExtraServicesModal(state, value) {
            state.extraServicesModal = value;
        },
        setActiveTab(state, activeTab) {
            state.activeTab = activeTab;
        },
        setCurrentIndex(state, value) {
            state.currentIndex = value;
        },

        addMainEquipment(state, equipment) {
            state.selectedEquipment.push({
                ...equipment,
                rowsCount:0,
                subEquipment: [
                ]
            });
        },
        incRowsInEquip(state,id) {
            const row = state.selectedEquipment.find(eq => eq.id == id);

            if(row) {
                row.rowsCount+= 1
            }
        },
        addExtraEquipment(state, { mainEquipmentId, subEquipment }) {
            const mainEquipment = state.selectedEquipment.find(eq => eq.id === mainEquipmentId);
            if (mainEquipment) {
                console.log(mainEquipment)
                mainEquipment.subEquipment.push(subEquipment);
            }
        },
        setActiveMainEquipment(state, equipmentId) {
            state.activeMainEquipmentId = equipmentId;
        },
        setActiveSubEquipment(state, equipmentId) {
            state.activeSubEquipmentId = equipmentId;
        },
        removeMainEquipment(state, index) {
            state.selectedServices.splice(index, 1);
        },

        removeExtraEquipment(state, { mainEquipmentId, extraEquipmentId }) {
            const mainEquipment = state.selectedServices.find(
                (eq) => eq.id === mainEquipmentId
            );
            if (mainEquipment) {
                mainEquipment.additionalEquipment = mainEquipment.additionalEquipment.filter(
                    (eq) => eq.id !== extraEquipmentId
                );
            }
        },
    },
    actions: {
        updateIncRowsInEquip({commit},id) {
            commit('incRowsInEquip', id);
        },
        setIncrementRowsCount(state) {
            state.rowsCount += 1;
        },
        setDecrementRowsCount(state) {
            state.rowsCount -= 1;
        },
        showModal({ commit }, value) {
            commit("setExtraServicesModal", value);
        },
        updateActiveMainEquipment({commit}, value) {
            commit('setActiveMainEquipment', value)
        },
        updateActiveSubEquipment({commit}, value) {
            commit('setActiveSubEquipment', value)
        },
        addMainEquipmentAction({ commit }, equipment) {
            commit("addMainEquipment", equipment);
        },

        addExtraEquipmentAction({ commit }, { mainEquipmentId, subEquipment }) {
            commit("addExtraEquipment", { mainEquipmentId, subEquipment });
        },

        removeMainEquipmentAction({ commit }, index) {
            commit("removeMainEquipment", index);
        },

        removeExtraEquipmentAction({ commit }, { mainEquipmentId, extraEquipmentId }) {
            commit("removeExtraEquipment", { mainEquipmentId, extraEquipmentId });
        },
    },
    getters: {
        getActiveSubEquipment: (state) => state.activeSubEquipmentId,
        getActiveMainEquipment: (state) => state.activeMainEquipmentId,
        getSelectedEquipment: (state) => state.selectedEquipment,
        getSelectedServices: (state) => state.selectedServices,
        getRowsCount: (state) => state.rowsCount,
        getExtraServicesModal: (state) => state.extraServicesModal,
        getCurrentIndex: (state) => state.currentIndex,
        getActiveTab: (state) => state.activeTab,
    },
};
