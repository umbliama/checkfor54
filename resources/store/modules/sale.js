export default {
    namespaced: true,
    state: () => ({
        activeTab: "sale",
        extraServices: [],
        currentIndex: 0,
        extraServicesModal: false,
        selectedServices: [],
        servicesRowsCount: 0,
    }),
    mutations: {
        incrementRowsCount(state) {
            state.servicesRowsCount += 1;
        },
        decrementRowsCount(state) {
            state.servicesRowsCount -= 1;
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
        updateExtraService(state, service) {
            state.extraServices = service;
        },
        addExtraService(state, service) {
            state.selectedServices.push(service);
        },
        setSelectedServicesObjectInfo(state, { index, field, value }) {
            if (state.extraServicesModal[index]) {
                state.extraServicesModal[index][field] = value;
            }
        },
        removeExtraService(state, index) {
            state.selectedServices.splice(index, 1);
        },
    },
    actions: {
        removeExtraServiceAction({ commit }, index) {
            commit("removeExtraService", index);
        },
        updateExtraServicesModal({ commit }, value) {
            try {
                fetch("/api/getExtraServices")
                    .then((res) => res.json())
                    .then((data) => commit("updateExtraService", data));
            } catch (e) {
                console.error(e);
            }
        },
        incCurrentIndex({ commit }) {
            commit("setCurrentIndex", 1);
        },
        decCurrentIndex({ commit }) {
            commit("setCurrentIndex", -1);
        },
        showModal({ commit }, value) {
            commit("setExtraServicesModal", value);
        },
        updateExtraServices({ commit }, service) {
            commit("addExtraService", service);
        },
        updateActiveTab({ commit }, payload) {
            commit("setActiveTab", payload);
        },
        updateSelectedServicesObject({ commit }, { index, field, value }) {
            commit("setSelectedServicesObjectInfo", { index, field, value });
        },
        updateIncRowsCount({ commit }) {
            commit("incrementRowsCount");
        },
        updateDecRowsCount({ commit }) {
            commit("decrementRowsCount");
        },
    },
    getters: {
        getRowsCount: (state) => state.servicesRowsCount,
        getSelectedServices: (state) => state.selectedServices,
        getExtraServicesModal: (state) => state.extraServicesModal,
        getCurrentIndex: (state) => state.currentIndex,
        getActiveTab: (state) => state.activeTab,
        getExtraServices: (state) => state.extraServices,
    },
};
