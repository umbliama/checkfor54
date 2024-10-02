import { EquipMenuItems } from "../../constants";

export default {
    namespaced: true,
    state: () => ({
        equipmentObject: {},
        categoryActive: 0,
        sizeActive: 0,
        seriesActive: '',
        menuActiveItem: EquipMenuItems.REPORT,
        inputLocationShown: false,
        dropDownIsShown: true,
        equipmentCategories: null,
        equipmentSizesCounts: null,
        equipmentCategoriesCounts: null,
        equipmentSizes: null,
        equipmentRepair: null,
        equipmentTest:null,
        equipmentReport: null
    }),
    mutations: {
        setEquipmentTest(state,tests) {
            state.equipmentTest = tests
        },
        setEquipmentRepair(state,repairs) {
            state.equipmentRepair = repairs
        },
        setEquipmentReport(state,reports) {
            state.equipmentReport = reports
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
        async updateEquipmentRepair({commit}, {category_id,size_id,series}) {
            try {
                const response = await fetch(`/api/equip/repair?category_id=${category_id}&size_id=${size_id}&series=${series}`);
                const data = await response.json();
                commit('setEquipmentRepair', data)
            } catch (error) {
                console.log(error);
            }

        },  
        async updateEquipmentReport({commit}, {category_id,size_id,series}) {
            try {
                const response = await fetch(`/api/equip/report?category_id=${category_id}&size_id=${size_id}&series=${series}`);
                const data = await response.json();
                commit('setEquipmentReport', data)
            } catch (error) {
                console.log(error);
            }

        },  
        async updateEquipmentTest({commit}, {category_id,size_id,series}) {
            try {
                const response = await fetch(`/api/equip/tests?category_id=${category_id}&size_id=${size_id}&series=${series}`);
                const data = await response.json();
                commit('setEquipmentTest', data)
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
        getEquipmentReports: (state) => state.equipmentReport
    },
};
