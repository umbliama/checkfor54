export default {
    namespaced: true,
    state: () => ({
        showForm: false,
        modalShown: false,
        equipmentCategories: null,
        equipmentSizesCounts: null,
        equipmentCategoriesCounts: null,
        equipmentSizes: null,
        selectedEquipment: null,
        selectedEquipmentService: null,
        equipmentObject: null,
        subRowsCount: 0,
        subEquipmentArray: [],
        subSelectedEquipment:[],
        subSelectedEquipmentObjects:[],
        selectedMonth: "all",
        selectedYear: new Date().getFullYear(),
        selectedActive:true
    }),
    mutations: {    
        setSelectedActive(state,value){
            state.selectedActive = value
        },
        setSelectedMonth(state,value){
            state.selectedMonth = value
        },
        decSelectedYear(state){
            state.selectedYear -= 1
        },
        incSelectedYear(state){
            state.selectedYear += 1
        },
        setSubSelectedEquipmentObjects(state,value){
            state.subSelectedEquipmentObjects.push(value)
        },
        updateSubSelectedEquipmentObject(state, { index, field, value }) {
            // Ensure that the object exists at the given index
            if (state.subSelectedEquipmentObjects[index]) {
                // Dynamically update the field of the object
                state.subSelectedEquipmentObjects[index][field] = value;
            }
        },
        setSubSelectedEquipment(state, value){
            state.subSelectedEquipment.push(value)
        },
        setSubEquipmentArray(state, equipment) {
            state.subEquipmentArray.push(equipment);
        },
        clearSubEquipmentData(state) {
            state.subEquipmentArray = [];
        },
        incrementSubRowsCount(state) {
            state.subRowsCount += 1;
        },
        decrementSubRowsCount(state) {
            state.subRowsCount -= 1;
        },
        setSelectedEquipment(state, value) {
            state.selectedEquipment = value;
        },
        setSelecetedEquipmentService(state, value) {
            state.selectedEquipmentService = value;
        },
        setModalShown(state, value) {
            state.modalShown = value;
        },
        setShowForm(state, value) {
            state.showForm = value;
        },
        setEquipmentSizesCounts(state, sizesCounts) {
            state.equipmentSizesCounts = sizesCounts;
        },
        setEquipmentSizes(state, sizes) {
            state.equipmentSizes = sizes;
        },
        setEquipmentCategoriesCounts(state, categoriesCounts) {
            state.equipmentCategoriesCounts = categoriesCounts;
        },
        setEquipmentCategories(state, categories) {
            state.equipmentCategories = categories;
        },
        setEquipment(state, equipment) {
            state.equipmentObject = equipment;
        },
    },
    actions: {
        async fetchServices({ state }) {
            const { selectedMonth, selectedYear } = state;
            
            // Call API with selectedMonth and selectedYear
            const response = await axios.get('/api/services', {
                params: {
                    month: selectedMonth,
                    year: selectedYear
                }
            });
            
            // Handle the response and update the service data in your store
        },    
        updateSelectedActive({commit}, value) {
            commit('setSelectedActive', value)
        },
        updateSelectedMonth({commit}, value ) {
            commit('setSelectedMonth', value)
        },
        updateDecSelectedYear({commit} ) {
            commit('decSelectedYear')
        },
        updateIncSelectedYear({commit} ) {
            commit('incSelectedYear')
        },
        updateSubSelectedEquipmentObjectsByKey({ commit }, { index, field, value }) {
            commit('updateSubSelectedEquipmentObject', { index, field, value });
        },
        updateSubSelectedEquipmentObjects({commit}, value) {
            commit('setSubSelectedEquipmentObjects', value);
        },
        updateSubEquipment({commit},value) {
            commit('setSubSelectedEquipment',value)
        },
        clearSubEquipmentArray({ commit }) {
            commit("clearSubEquipmentData");
        },
        updateSubEquipmentArray({ commit }, equipment) {
            commit("setSubEquipmentArray", equipment);
        },
        updateIncSubRowsCount({ commit }) {
            commit("incrementSubRowsCount");
        },
        updateDecSubRowsCount({ commit }) {
            commit("decrementSubRowsCount");
        },
        updateSelectedEquipment({ commit }, value) {
            commit("setSelectedEquipment", value);
        },
        updateSelectedEquipmentService({ commit }, value) {
            commit("setSelecetedEquipmentService", value);
        },
        updateModalShown({ commit }, value) {
            commit("setModalShown", value);
        },
        updateShowForm({ commit }, value) {
            commit("setShowForm", value);
        },
        async fetchEquipmentCategoriesCount({ commit }) {
            try {
                const response = await fetch(`/api/equipment/categories/count`);
                const data = await response.json();
                commit("setEquipmentCategoriesCounts", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipmentCategories({ commit }) {
            try {
                const response = await fetch(`/api/equipment/categories`);
                const data = await response.json();
                commit("setEquipmentCategories", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipmentSizesCount({ commit }) {
            try {
                const response = await fetch(`/api/equipment/sizes/count`);
                const data = await response.json();
                commit("setEquipmentSizesCounts", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipmentSizes({ commit }) {
            try {
                const response = await fetch(`/api/equipment/sizes`);
                const data = await response.json();
                commit("setEquipmentSizes", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipmentSizesById({ commit },categoryId) {
            try {
                const response = await fetch(`/api/equipment/sizes/${categoryId}`);
                const data = await response.json();
                commit("setEquipmentSizes", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipmentP({ commit }, page = 1) {
            try {
                const response = await fetch(`/api/equipment?page=${page}`);
                const data = await response.json();
                commit("setEquipment", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipment({ commit }, { category_id, size_id }) {
            try {
                const response = await fetch(
                    `/api/equipment/${category_id}/${size_id}`
                );
                const data = await response.json();
                commit("setEquipment", data);
            } catch (error) {
                console.log(error);
            }
        },
    },
    getters: {
        getSelectedActive: (state) => state.selectedActive,
        getSelectedYear: (state) => state.selectedYear,
        getSelectedMonth: (state) => state.selectedMonth,
        getSubSelectedEquipmentObjects: (state) => state.subSelectedEquipmentObjects,
        getSubEquipment: (state) => state.subSelectedEquipment,
        getSubEquipmentArray: (state) => state.subEquipmentArray,
        getsubRowsCount: (state) => state.subRowsCount,
        getSelectedEquipmentService: (state) => state.selectedEquipmentService,
        getSelectedEquipment: (state) => state.selectedEquipment,
        getModalShown: (state) => state.modalShown,
        getShowForm: (state) => state.showForm,
        getEquipment: (state) => state.equipmentObject,
        getEquipmentCategories: (state) => state.equipmentCategories,
        getEquipmentCategoriesCounts: (state) =>
            state.equipmentCategoriesCounts,
        getEquipmentSizes: (state) => state.equipmentSizes,
        getEquipmentSizesCounts: (state) => state.equipmentSizesCounts,
    },
};
