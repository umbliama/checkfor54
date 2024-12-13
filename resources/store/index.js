import { SideMenuMenuStates } from "../constants/index";
import { createStore } from "vuex";
import pagination from "./modules/pagination";
import equipment from "./modules/equipment";
import contragent from "./modules/contragent";
import dashboard from "./modules/dashboard";
import services from "./modules/services";
import notifications from "./modules/notifications";
import incident from "./modules/incident";
import sale from "./modules/sale";



const store = createStore({
    modules: {
        pagination,
        equipment,
        contragent,
        services,
        dashboard,
        notifications,
        incident,
        sale
    },
    state: {
        cities: [],
        activeSideMenuItem: SideMenuMenuStates.DASHBOARD,
        equimpent: [],
        userId:null,
        globalSearch:[]
    },
    mutations: {
        SET_GLOBAL_SEARCH(state,value){
            state.globalSearch = value
        },
        SET_USER_ID(state,userId) {
            state.userId = userId
        },
        SET_ACTIVE_CITY(state, city) {
            state.activeCity = city;
        },
        SET_EQUIPMENT(state, equimpent) {
            state.equimpent = equimpent;
        },
        SET_CITIES(state, cities) {
            state.cities = cities;
        },
        SET_ACTIVE_SIDEMENU_ITEM(state, item) {
            state.activeSideMenuItem = item;
        },
    },
    actions: {
        async fetchLocations({ commit }) {
            try {
                const response = await fetch("/api/cities");
                const data = await response.json();
                commit("SET_CITIES", data);
            } catch (error) {
                console.log(error);
            }
        },
        updateGlobalSearch({commit}, value){
            commit('SET_GLOBAL_SEARCH', value)
        },
        updateUserId({commit}, userId) {
            commit('SET_USER_ID', userId)
        },

        setActiveSidemenuItem({ commit }, item) {
            commit("SET_ACTIVE_SIDEMENU_ITEM", item);
        },
        setActiveCity({ commit }, city) {
            commit("SET_ACTIVE_CITY", city);
        },
    },
    getters: {
        getGlobalSearch: (state) => state.globalSearch,
        getUserId: (state) => state.userId,
        activeCity: (state) => state.activeCity,
        cities: (state) => state.cities,
        activeSidemenuItem: (state) => state.activeSideMenuItem,
    },
});

export default store;
