import { SideMenuMenuStates } from "../constants/index";
import { createStore } from "vuex";
import pagination from "./modules/pagination";
import equipment from "./modules/equipment";
import contragent from "./modules/contragent";
import dashboard from "./modules/dashboard";
import services from "./modules/services";
import notifications from "./modules/notifications";



const store = createStore({
    modules: {
        pagination,
        equipment,
        contragent, 
        services,
        dashboard,
        notifications
    },
    state: {
        cities: [],
        activeSideMenuItem: SideMenuMenuStates.DASHBOARD,
        equimpent: [],
    },
    mutations: {
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

        setActiveSidemenuItem({ commit }, item) {
            commit("SET_ACTIVE_SIDEMENU_ITEM", item);
        },
        setActiveCity({ commit }, city) {
            commit("SET_ACTIVE_CITY", city);
        },
    },
    getters: {
        activeCity: (state) => state.activeCity,
        cities: (state) => state.cities,
        activeSidemenuItem: (state) => state.activeSideMenuItem,
    },
});

export default store;
