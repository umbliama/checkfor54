import { EquipMenuItems } from "../../constants";

export default {
    namespaced: true,
    state: () => ({
        equipmentObject: {},
        categoryActive: null,
        sizeActive: null,
        seriesActive: "",
        menuActiveItem: EquipMenuItems.REPORT,
        locationActive: 0,
        inputLocationShown: false,
        dropDownIsShown: true,
        equipmentCategories: null,
        equipmentSizesCounts: null,
        equipmentCategoriesCounts: null,
        equipmentSizes: null,
        equipmentRepair: null,
        equipmentTest: null,
        equipmentReport: null,
        equipmentHistory: null,
        equipmentCount: 0,
        equipmentRepairCount: 0,
        equipmentTestCount: 0,
        priceTabActive: "price",
        locationModal: false,
        errors: {},
        inputHyperLinkShown: false,
        selectedId: null,
        sortBy: "",
        sortOrder: "asc",
        filterShown:false,
        priceRowsCount:0,

    }),
    mutations: {
        incPriceRowsCount:(state) => {
            state.priceRowsCount += 1
        },
        decPriceRowsCount:(state) => {
            state.priceRowsCount -= 1
        },
        setFilterShown:(state) => {
            state.filterShown = !state.filterShown
        },
        setSortBy: (state, value) => {
            state.sortBy = value;
        },
        setSortOrder: (state, value) => {
            state.sortOrder = value;
        },
        setSelectedId(state, value) {
            state.selectedId = value;
        },
        toggleInputHyperLinkShown(state) {
            state.inputHyperLinkShown = !state.inputHyperLinkShown;
        },
        setErrors(state, value) {
            state.errors = value;
        },
        setLocationModal(state, value) {
            state.locationModal = value;
        },
        setLocationActive(state, value) {
            state.locationActive = value;
        },
        setPriceTabActive(state, value) {
            state.priceTabActive = value;
        },
        setEquipmentTestCount(state, value) {
            state.equipmentTestCount = value;
        },
        setEquipmentRepairCount(state, value) {
            state.equipmentRepairCount = value;
        },
        setEquipmentCount(state, value) {
            state.equipmentCount = value;
        },
        setEquipmentTest(state, tests) {
            state.equipmentTest = tests;
        },
        clearSizeActive(state) {
            state.sizeActive = 0;
        },
        clearSeriesActive(state) {
            state.seriesActive = "";
        },
        clearCategoryActive(state) {
            state.categoryActive = 0;
        },
        clearEquipmentTest(state) {
            state.equipmentTest = null;
        },
        clearEquipmentRepairs(state) {
            state.equipmentRepair = null;
        },
        setEquipmentRepair(state, repairs) {
            state.equipmentRepair = repairs;
        },
        setEquipmentReport(state, reports) {
            state.equipmentReport = reports;
        },
        setEquipmentHistory(state, reports) {
            state.equipmentHistory = reports;
        },
        setSeriesId(state, seriesId) {
            state.seriesActive = seriesId;
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
        setCategoryActive(state, category) {
            state.categoryActive = category;
        },
        toggleInputLocationShown(state, value) {
            state.inputLocationShown = value;
        },
        setEquipment(state, equipment) {
            state.equipmentObject = equipment;
        },
        setSizeActive(state, size) {
            state.sizeActive = size;
        },
        setMenuActiveItem(state, item) {
            state.menuActiveItem = item;
        },
    },
    actions: {
        updatePriceRowsCountInc({commit}) {
            commit('incPriceRowsCount')
        },
        updatePriceRowsCountDec({commit}) {
            commit('decPriceRowsCount')
        },

        updateFilterShown({commit}){
            commit('setFilterShown')
        },
        updateSortBy({commit}, payload) {
            commit('setSortBy',payload)
        },
        updateSortOrder({commit}, payload) {
            commit("setSortOrder", payload)
        },

        async updateSelectedId({ commit }, value) {
            commit("setSelectedId", value);
        },
        async updateInputHyperLinkShown({ commit }, value) {
            commit("toggleInputHyperLinkShown", value);
        },
        async updateErrors({ commit }, value) {
            commit("setErrors", value);
        },
        async updateLocationModal({ commit }, value) {
            commit("setLocationModal", value);
        },
        async updateLocationActive({ commit }, value) {
            commit("setLocationActive", value);
        },
        async updatePriceTabActive({ commit }, value) {
            commit("setPriceTabActive", value);
        },
        async updateEquipmentTestCount({ commit }) {
            try {
                const response = await fetch(`/api/equipment/test/getCount`);
                const data = await response.json();
                commit("setEquipmentTestCount", data);
            } catch (error) {
                console.log(error);
            }
        },
        async updateEquipmentRepairCount({ commit }) {
            try {
                const response = await fetch(`/api/equipment/repair/getCount`);
                const data = await response.json();
                commit("setEquipmentRepairCount", data);
            } catch (error) {
                console.log(error);
            }
        },
        async updateEquipmentCount({ commit }) {
            try {
                const response = await fetch(`/api/equipment/getCount`);
                const data = await response.json();
                commit("setEquipmentCount", data);
            } catch (error) {
                console.log(error);
            }
        },
        async downgradeSizeActive({ commit }) {
            commit("clearSizeActive");
        },
        async downgradeSeriesActive({ commit }) {
            commit("clearSeriesActive");
        },
        async downgradeCategoryActive({ commit }) {
            commit("clearCategoryActive");
        },
        async downgradeEquipmentRepairs({ commit }) {
            commit("clearEquipmentRepairs");
        },
        async downgradeEquipmentTests({ commit }) {
            commit("clearEquipmentTest");
        },
        async updateEquipmentRepair(
            { commit },
            { category_id, size_id, series }
        ) {
            try {
                const response = await fetch(
                    `/api/equip/repair?category_id=${category_id}&size_id=${size_id}&series=${series}`
                );
                const data = await response.json();
                commit("setEquipmentRepair", data);
            } catch (error) {
                console.log(error);
            }
        },
        async updateEquipmentReport(
            { commit },
            { category_id, size_id, series }
        ) {
            try {
                const response = await fetch(
                    `/api/equip/report?category_id=${category_id}&size_id=${size_id}&series=${series}`
                );
                const data = await response.json();
                commit("setEquipmentReport", data);
            } catch (error) {
                console.log(error);
            }
        },
        async updateEquipmentHistory(
            { commit },
            { category_id, size_id, series }
        ) {
            try {
                const response = await fetch(
                    `/api/equip/services?category_id=${category_id}&size_id=${size_id}&series=${series}`
                );
                const data = await response.json();
                commit("setEquipmentHistory", data);
            } catch (error) {
                console.log(error);
            }
        },
        async updateEquipmentTest(
            { commit },
            { category_id, size_id, series }
        ) {
            try {
                const response = await fetch(
                    `/api/equip/tests?category_id=${category_id}&size_id=${size_id}&series=${series}`
                );
                const data = await response.json();
                commit("setEquipmentTest", data);
            } catch (error) {
                console.log(error);
            }
        },
        async updateRepairCategoryId({ commit }, categoryId) {
            commit("setRepairCategoryId", categoryId);
        },
        async updateSeriesId({ commit }, seriesId) {
            commit("setSeriesId", seriesId);
        },
        async updateRepairSizeId({ commit }, sizeId) {
            commit("setRepairSizeId", sizeId);
        },
        async updateEquipmentSizesCounts({ commit }, sizes) {
            commit("setEquipmentSizesCounts", sizes);
        },
        async updateEquipmentSizes({ commit }, sizes) {
            commit("setEquipmentSizes", sizes);
        },
        async updateEquipmentCategoriesCounts({ commit }, categories) {
            commit("setEquipmentCategoriesCounts", categories);
        },
        async updateEquipmentCategories({ commit }, categories) {
            commit("setEquipmentCategories", categories);
        },
        async updateMenuItem({ commit }, item) {
            commit("setMenuActiveItem", item);
        },
        async updateInputLocationShown({ commit }, value) {
            commit("toggleInputLocationShown", value);
        },
        async updateCategory({ commit }, category) {
            commit("setCategoryActive", category);
        },
        async updateSize({ commit }, size) {
            commit("setSizeActive", size);
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
        async fetchEquipmentCategoriesCount({ commit }) {
            try {
                const response = await fetch(`/api/equipment/categories/count`);
                const data = await response.json();
                commit("setEquipmentCategoriesCounts", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipment({ commit }, page = 1) {
            try {
                const response = await fetch(`/api/equipment?page=${page}`);
                const data = await response.json();
                commit("setEquipment", data);
            } catch (error) {
                console.log(error);
            }
        },
        async fetchEquipmentByID({ commit }, categoryId) {
            try {
                const response = await fetch(`/api/equipment/${categoryId}`);
                const data = await response.json();
                commit("setEquipment", data);
            } catch (error) {
                console.log(error);
            }
        },
    },
    getters: {
        getPriceRowsCount: state => state.priceRowsCount,
        getFilterShown: state => state.filterShown,
        getSortOrder: state => state.sortOrder,
        getSortBy: state => state.sortBy,
        getSelectedID: (state) => state.selectedId,
        getInputHyperLinkShown: (state) => state.inputHyperLinkShown,
        getErrors: (state) => state.errors,
        getLocationModal: (state) => state.locationModal,
        getLocationActive: (state) => state.locationActive,
        getPriceTabActive: (state) => state.priceTabActive,
        getEquipmentCount: (state) => state.equipmentCount,
        getEquipmentRepairCount: (state) => state.equipmentRepairCount,
        getEquipmentTestCount: (state) => state.equipmentTestCount,
        getCategoryActive: (state) => state.categoryActive,
        getEquipment: (state) => state.equipmentObject,
        getSizeActive: (state) => state.sizeActive,
        getMenuActiveItem: (state) => state.menuActiveItem,
        getInputLocationShown: (state) => state.inputLocationShown,
        getDropdownShown: (state) => state.dropDownIsShown,
        getEquipmentCategories: (state) => state.equipmentCategories,
        getEquipmentSizesCounts: (state) => state.equipmentSizesCounts,
        getEquipmentCategoriesCounts: (state) =>
            state.equipmentCategoriesCounts,
        getEquipmentSizes: (state) => state.equipmentSizes,
        getSeriesActive: (state) => state.seriesActive,
        getEquipmentRepairs: (state) => state.equipmentRepair,
        getEquipmentTests: (state) => state.equipmentTest,
        getEquipmentHistory: (state) => state.equipmentHistory,
        getEquipmentReports: (state) => state.equipmentReport,
    },
};
