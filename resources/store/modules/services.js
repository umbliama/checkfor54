export default {
    namespaced: true,
    state: () => ({
        showForm: false,
        modalShown: false,
        equipmentCategories: null,
        equipmentSizesCounts: null,
        equipmentCategoriesCounts: null,
        equipmentSizes: null,
        selectedEquipment: [],
        selectedEquipmentService: [],
        equipmentObject: null,
        subRowsCount: 0,
        subEquipmentArray: [],
        subSelectedEquipment: [],
        subSelectedEquipmentObjects: [],
        selectedMonth: "all",
        selectedYear: new Date().getFullYear(),
        selectedActive: true,
        equipmentType: 0,
        chosenEquipment: null,
        activeEquipmentId: null,
        activeSubEquipmentId: null,
        selectedServices: [],
    }),
    mutations: {
        setSelectedServices(state, value) {
            state.selectedServices.push({ ...value, requestData: {} });
        },
        setActiveSubEquipmentId(state, value) {
            state.activeSubEquipmentId = value;
        },
        setActiveEquipmentId(state, value) {
            state.activeEquipmentId = value;
        },
        setChosenEquipment(state, value) {
            state.chosenEquipment = value;
        },
        setEquipmentType(state, value) {
            state.equipmentType = value;
        },
        setSelectedActive(state, value) {
            state.selectedActive = value;
        },
        setSelectedMonth(state, value) {
            state.selectedMonth = value;
        },
        decSelectedYear(state) {
            state.selectedYear -= 1;
        },
        incSelectedYear(state) {
            state.selectedYear += 1;
        },
        setSubSelectedEquipmentObjects(state, value) {
            state.subSelectedEquipmentObjects.push(value);
        },
        updateSubSelectedEquipmentObject(state, { index, field, value }) {
            if (state.subSelectedEquipmentObjects[index]) {
                state.subSelectedEquipmentObjects[index][field] = value;
            }
        },
        setSubSelectedEquipment(state, value) {
            state.subSelectedEquipment.push(value);
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
            state.selectedEquipment.push(value);
        },
        setSelecetedEquipmentService(state, value) {
            const equipmentObject = {
                ...value,
                subEquipment: [],
            };
            state.selectedEquipmentService.push(equipmentObject);
        },
        addSubEquipmentToSelected(state, { equipment_id, subEquipmentItem }) {
            const equipment = state.selectedEquipmentService.find(
                (eq) => eq.id === equipment_id.value
            );
            console.log(
                `${subEquipmentItem} has been added to ${equipment.subEquipment} by ${equipment_id}`
            );

            if (equipment) {
                equipment.subEquipment.push(subEquipmentItem);
            } else {
                console.warn(`Equipment with ID ${equipment_id} not found.`);
            }
            console.log(`${subEquipmentItem} has been added to ${equipment}`);
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
        async updateSelectedServices({ commit }, value) {
            commit("setSelectedServices", value);
        },
        async updateActiveEquipmentId({ commit }, value) {
            commit("setActiveEquipmentId", value);
        },
        async updateActiveSubEquipmentId({ commit }, value) {
            commit("setActiveSubEquipmentId", value);
        },
        async updateChosenEquipment({ commit }, value) {
            commit("setChosenEquipment", value);
        },
        async fetchServices({ state }) {
            const { selectedMonth, selectedYear } = state;

            const response = await axios.get("/api/services", {
                params: {
                    month: selectedMonth,
                    year: selectedYear,
                },
            });
        },
        updateSubSelectedEquipment(
            { commit },
            { equipment_id, subEquipmentItem }
        ) {
            commit("addSubEquipmentToSelected", {
                equipment_id,
                subEquipmentItem,
            });
        },
        updateEquipmentType({ commit }, value) {
            commit("setEquipmentType", value);
        },
        updateSelectedActive({ commit }, value) {
            commit("setSelectedActive", value);
        },
        updateSelectedMonth({ commit }, value) {
            commit("setSelectedMonth", value);
        },
        updateDecSelectedYear({ commit }) {
            commit("decSelectedYear");
        },
        updateIncSelectedYear({ commit }) {
            commit("incSelectedYear");
        },
        updateSubSelectedEquipmentObjectsByKey(
            { commit },
            { index, field, value }
        ) {
            commit("updateSubSelectedEquipmentObject", { index, field, value });
        },
        updateSubSelectedEquipmentObjects({ commit }, value) {
            commit("setSubSelectedEquipmentObjects", value);
        },
        updateSubEquipment({ commit }, value) {
            commit("setSubSelectedEquipment", value);
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
        async fetchEquipmentSizesById({ commit }, categoryId) {
            try {
                const response = await fetch(
                    `/api/equipment/sizes/${categoryId}`
                );
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
        getActiveSubEquipmentId: (state) => state.activeSubEquipmentId,
        getActiveEquipmentId: (state) => state.activeEquipmentId,
        getChosenEquipment: (state) => state.chosenEquipment,
        getEquipmentType: (state) => state.equipmentType,
        getSelectedActive: (state) => state.selectedActive,
        getSelectedYear: (state) => state.selectedYear,
        getSelectedMonth: (state) => state.selectedMonth,
        getSubSelectedEquipmentObjects: (state) =>
            state.subSelectedEquipmentObjects,
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
        getSelectedServices: (state) => state.selectedServices,

    },
};
