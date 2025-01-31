import axios from "axios";

export default {
    namespaced: true,
    state: () => ({
        activeTab: "sale",
        currentIndex: 0,
        rowsCount: 0,
        servicesRows:0,
        extraServicesModal: false,
        extraServices:[],
        selectedServices: [],
        selectedEquipment: [],
        activeMainEquipmentId: null,
        activeSubEquipmentId: null,
    }),
    mutations: {
        setSelectedServices(state, value ) {
            state.selectedServices.push({...value, requestData:{}})
        },
        
        setSelectedServicesData(state, { id, field, value }) {
            const service = state.selectedServices.find(eq => eq.id === id);
            if (service) {
                service.requestData[field] =  value
                console.log(service.requestData)

            }
        },
        setExtraServices(state,value) {
            axios.get('/api/getExtraServices').then(response => response.data).then(data => state.extraServices =  data)
        },
        incrementRowsCount(state) {
            state.rowsCount += 1;
        },
        decrementRowsCount(state) {
            state.rowsCount -= 1;
        },
        setExtraServicesModal(state, value) {
            state.extraServicesModal = value;
        },
        setIncServicesRows(state) {
            state.servicesRows += 1;
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
                rowsCount: 0,
                requestData: {},
                subEquipment: [
                    
                ]
            });
        },
        addRequestDataToEquipment(state, { id, field, data }) {
            const equipment = state.selectedEquipment.find(eq => eq.id == id);

            if (equipment) {
                equipment.requestData[field] = data
            }
        },
        addRequestDataToSubEquipment(state, { mainId, id, field, data }) {
            const equipment = state.selectedEquipment[mainId].subEquipment[id];
            if (equipment) {
                equipment.requestData[field] = data
            }
        },
        incRowsInEquip(state, id) {
            const row = state.selectedEquipment.find(eq => eq.id == id);

            if (row) {
                row.rowsCount += 1
            }
        },
        addExtraEquipment(state, { mainEquipmentId, subEquipment }) {
            const mainEquipment = state.selectedEquipment.find(eq => eq.id === mainEquipmentId);
            if (mainEquipment) {
                mainEquipment.subEquipment.push({...subEquipment,requestData:{}});
            }
        },
        setActiveMainEquipment(state, equipmentId) {
            state.activeMainEquipmentId = equipmentId;
        },
        clearActiveMainEquipment(state) {
            state.activeMainEquipmentId = null;
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
        updateSelectedServicesData({commit}, {id, field, value}) {
            console.log({id, field, value})
            commit('setSelectedServicesData', {id,field,value} )
        },
        updateSelectedServices({commit}, value) {
            commit('setSelectedServices', value)
        },
        updateExtraServices({commit}) {
            commit('setExtraServices')
        },
        updateIncRowsCount({commit}) {
            commit('setIncServicesRows');
        },
        updateRequestDataInEquipment({ commit }, { id, field,data }) {
            commit('addRequestDataToEquipment', { id, field,data })
        },
        updateRequestDataInSubEquipment({ commit }, { mainId, id, field,data }) {
            commit('addRequestDataToSubEquipment', { mainId, id, field,data })
        },
        updateIncRowsInEquip({ commit }, id) {
            commit('incRowsInEquip', id);
        },
        setIncrementRowsCount({ commit }) {
            commit('incrementRowsCount');
        },
        setDecrementRowsCount(state) {
            state.rowsCount -= 1;
        },
        showModal({ commit }, value) {
            commit("setExtraServicesModal", value);
        },
        updateActiveMainEquipment({ commit }, value) {
            commit('setActiveMainEquipment', value)
        },
        clearActiveMainEquipment({ commit }, value) {
            commit('clearActiveMainEquipment')
        },
        updateActiveSubEquipment({ commit }, value) {
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
        updateActiveTab({ commit }, tab) {
            commit("setActiveTab", tab);
        },
    },
    getters: {
        getExtraServices: state => state.extraServices,
        getServicesRows: state => state.servicesRows,
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
